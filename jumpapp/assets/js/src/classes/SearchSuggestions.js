/**
 *      ██ ██    ██ ███    ███ ██████
 *      ██ ██    ██ ████  ████ ██   ██
 *      ██ ██    ██ ██ ████ ██ ██████
 * ██   ██ ██    ██ ██  ██  ██ ██
 *  █████   ██████  ██      ██ ██
 *
 * @author Dale Davies <dale@daledavies.co.uk>
 * @copyright Copyright (c) 2022, Dale Davies
 * @license MIT
 */

/**
 * Generate search suggestions.
 */
export default class SearchSuggestions {

    constructor(searchengines, inputelm, containerelm, eventemitter) {
        this.containerelm = containerelm;
        this.eventemitter = eventemitter;
        this.inputelm = inputelm;
        this.suggestionslistelm = containerelm.querySelector('.suggestion-list');
        this.searchproviderlist = null;
        this.searchengines = searchengines;
    }

    build_searchprovider_list_elm(query) {
        const searchproviderlist = document.createElement('ul');
        searchproviderlist.classList.add('searchproviders');
        searchproviderlist.setAttribute('tabindex', -1);
        this.searchengines.forEach((provider) => {
            const searchprovider = document.createElement('li');
            searchprovider.setAttribute('tabindex', -1);
            searchprovider.innerHTML = '<a target="_blank" rel="noopener" \
                href="'+provider.url+encodeURIComponent(query)+'"><span>Search on</span> '+provider.name+'</a>';
            searchproviderlist.appendChild(searchprovider);
        });
        searchproviderlist.addEventListener('keyup', e => {
            switch (e.code) {
                case 'ArrowUp':
                    if (document.activeElement == e.target.parentNode.parentNode.firstChild.firstChild) {
                        this.inputelm.focus();
                        break;
                    }
                    document.activeElement.parentNode.previousSibling.firstChild.focus();
                    break;
                case 'ArrowDown':
                    if (document.activeElement == e.target.parentNode.parentNode.lastChild.firstChild) {
                        const suggestionselm = document.querySelector('.suggestionholder .suggestions');
                        if (suggestionselm) {
                            suggestionselm.firstChild.firstChild.focus();
                        } else {
                            e.target.parentNode.parentNode.firstChild.firstChild.focus();
                        }
                        break;
                    }
                    document.activeElement.parentNode.nextSibling.firstChild.focus();
                    break;
            }
        });
        return searchproviderlist;
    }

    build_suggestion_list_elm(siteresults) {
        const suggestionslist = document.createElement('ul');
        suggestionslist.classList.add('suggestions');
        suggestionslist.setAttribute('tabindex', -1);
        siteresults.forEach((result) => {
            const resultitem = document.createElement('li');
            resultitem.setAttribute('tabindex', -1);
            resultitem.innerHTML = '<a target="_blank" rel="noopener" href="'+result.url+'">\
                <img class="icon" src="'+result.iconurl+'"><span class="name">'+result.name+'</span>';
            suggestionslist.appendChild(resultitem);
        });
        suggestionslist.addEventListener('keyup', e => {
            switch (e.code) {
                case 'ArrowUp':
                    if (document.activeElement == e.target.parentNode.parentNode.firstChild.firstChild) {
                        this.searchproviderlist.lastChild.firstChild.focus();
                        break;
                    }
                    document.activeElement.parentNode.previousSibling.firstChild.focus();
                    break;
                case 'ArrowDown':
                    if (document.activeElement == e.target.parentNode.parentNode.lastChild.firstChild) {
                        this.searchproviderlist.firstChild.firstChild.focus();
                        break;
                    }
                    document.activeElement.parentNode.nextSibling.firstChild.focus();
                    break;
            }
        });
        return suggestionslist;
    }

    replace(siteresults) {
        const newsuggestionslist = this.build_suggestion_list_elm(siteresults);

        const suggestionholder = document.createElement('span');
        suggestionholder.classList.add('suggestionholder');

        if (this.inputelm.value !== '') {
            const searchtitle = document.createElement('span');
            searchtitle.classList.add('suggestiontitle');
            searchtitle.innerHTML = 'Search';
            suggestionholder.appendChild(searchtitle);
            this.searchproviderlist = this.build_searchprovider_list_elm(this.inputelm.value);
            suggestionholder.appendChild(this.searchproviderlist);
        }

        if (newsuggestionslist.childNodes.length > 0) {
            const suggestiontitle = document.createElement('span');
            suggestiontitle.classList.add('suggestiontitle');
            suggestiontitle.innerHTML = 'Sites';
            suggestionholder.appendChild(suggestiontitle);
            suggestionholder.appendChild(newsuggestionslist)
        }

        if (suggestionholder.childNodes.length > 0) {
            this.containerelm.classList.add('suggestions');
            this.suggestionslistelm.replaceChildren(suggestionholder);
        } else {
            this.containerelm.classList.remove('suggestions');
            let suggestions = this.containerelm.querySelector('.suggestionholder');
            if (suggestions) {
                suggestions.remove();
            }
        }
    }
}
