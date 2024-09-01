/**
 * Add key binds to Jump, allowing the user to navigate
 * through tags and sites using their keyboards. When the
 * user presses "S", the search box is opened. When the
 * user presses "T", the tag dropdown is opened and the
 * arrow keys can be used to cycle through the tags. When
 * the tag list is closed, the sites will be navigated
 * with the arrow keys instead. Pressing ENTER will open
 * the selected tag or site, pressing ESCAPE will deselect
 * both active tag and site and close both the search box
 * and the tag list. The user can also open a site by
 * pressing CTRL + number. When a site is found at the
 * pressed number, it will be opened. Numbers start at 1
 * and end at 0 (ten).
 */
export default class KeyBinds {
    constructor() {
        this.keys = new Map();
        this.numericKeys = ["1", "2", "3", "4", "5", "6", "7", "8", "9", "0"];
        this.searchOpen = false;
        this.activeEvent = null;

        this.tags = document.getElementById("tags");
        this.search = document.querySelector(".search");

        // Watch for the opening of the search box. When it's open, we shouldn't interfere with the input.
        this.observer = new MutationObserver((mutations) => {
            this.searchOpen = mutations.some((m) => m.attributeName === "class" && m.target?.classList.contains("open"));
        });
    }

    /**
     * Initialise the key binds by adding event listeners.
     */
    init() {
        document.addEventListener("keydown", (e) => {
            this.activeEvent = e;
            this.keys.set(e.key, true);
            this.process();
        });

        document.addEventListener("keyup", (e) => {
            this.activeEvent = null;
            this.keys.set(e.key, false);
        });

        if (this.search) {
            this.observer.disconnect();
            this.observer.observe(this.search, {attributes: true});
        }
    }

    /**
     * Determine whether to directly open a site or navigate the tags or sites.
     */
    process() {
        if (this.keys.get("Control")) {
            this.activate_site();
        } else {
            this.parse_navigation();
        }
    }

    /**
     * Activate the site for the number that was pressed on the keyboard.
     */
    activate_site() {
        // Determine the number that was pressed.
        const site = this.numericKeys.find((n) => this.keys.get(n) === true);

        if (site) {
            // Convert 0 to 10, so ten shortcuts can be offered with 1 being the first.
            const num = site === "0" ? "10" : site;

            // Find the site for the number. When none is found, the result will be null.
            const anchor = document.querySelector(`ul.sites li:nth-child(${num}) a`);

            // Trigger the anchor of the site, if any.
            if (anchor) {
                anchor.click();
            }
        }
    }

    /**
     * Parse the active key to determine how to navigate.
     */
    parse_navigation() {
        // The ESCAPE key will deselect any active tag or site and close the tags list and search window.
        if (this.keys.get("Escape")) {
            return this.escape_press();
        }

        // Ignore any of the following when the search window is open.
        if (this.searchOpen) {
            return;
        }

        // The S key is bound to opening the search window.
        if (this.keys.get("s")) {
            return this.toggle_search();
        }

        // The T key is bound to opening and closing the tags list.
        if (this.keys.get("t")) {
            return this.toggle_tags();
        }

        // The arrow keys will cycle through tags or sites.
        if (this.keys.get("ArrowUp") || this.keys.get("ArrowDown") || this.keys.get("ArrowLeft") || this.keys.get("ArrowRight")) {
            return this.arrow_press();
        }

        // The ENTER key will activate a selected tag or site.
        if (this.keys.get("Enter")) {
            return this.enter_press();
        }
    }

    /**
     * Close both search and tag windows.
     */
    escape_press() {
        document.querySelectorAll("a.active").forEach(a => a.classList.remove("active"));
        this.tags.classList.remove("enable");
        this.search?.classList.remove("open", "suggestions");
        this.search?.querySelector(".suggestion-list").childNodes.forEach((n) => n.parentNode.removeChild(n));
    }

    /**
     * Process the user pressing the ENTER button. Depending on the context, a tag or site link is clicked.
     */
    enter_press() {
        // Determine whether tags or sites should be triggered.
        if (this.tags.classList.contains("enable")) {
            this.tags.querySelector("a.active")?.click();
        } else {
            document.querySelector("ul.sites a.active")?.click();
        }
    }

    /**
     * Process the user pressing an arrow button, which cycles through either tags or sites.
     */
    arrow_press() {
        // Determine whether tags or sites should be cycled through.
        if (this.tags.classList.contains("enable")) {
            this.navigate_elements("#tags", this.keys.get("ArrowDown") || this.keys.get("ArrowRight")); 
        } else {
            this.navigate_elements("ul.sites", this.keys.get("ArrowDown") || this.keys.get("ArrowRight"));
        }
    }

    /**
     * Toggle the visibility of the tags list.
     */
    toggle_tags() {
        this.tags.classList.toggle("enable");

        // Mark the active tag so the user is aware where navigation begins.
        if (this.tags.classList.contains("enable") && this.tags.querySelector("a.active") === null) {
            // Try and read the tag from the URL. If that fails, select the first one.
            const tag = document.location.pathname.split("/").filter(x => !!x).pop();

            if (tag) {
                this.tags.querySelectorAll("a").forEach(a => {
                    if (a.textContent === tag) {
                        a.classList.add("active");
                    }
                });
            } else {
                this.tags.querySelector("a").classList.add("active");
            }
        }
    }

    /**
     * Toggle the visibility of the search box, if it exists.
     */
    toggle_search() {
        if (this.search) {
            this.activeEvent?.preventDefault();
            this.search.classList.add("open");
            this.search.querySelector("input[type=\"search\"]").focus();
        }
    }

    /**
     * Navigate through tags or sites.
     * @param containerSelector {string} The CSS selector for the container in which the anchors can be found.
     * @param forward {boolean} Whether we are cycling forward through the list of anchors or not.
     */
    navigate_elements(containerSelector, forward) {
        let newEl;

        // Find the currently active anchor. When found, the next link will be determined, otherwise
        // the first or last, based on the value of "forward", will be selected.
        const activeEl = document.querySelector(`${containerSelector} a.active`);

        // Function that returns the default element that should be activated if none is found by
        // other means.
        const getDefault = () => document.querySelector(`${containerSelector} li:${forward ? "first" : "last"}-of-type a`);

        if (activeEl) {
            activeEl.classList.toggle("active");
            newEl = (forward ? activeEl.parentElement.nextElementSibling : activeEl.parentElement.previousElementSibling)?.querySelector("a") || getDefault();
        } else {
            newEl = getDefault();
        }

        newEl?.classList.toggle("active");
    }
}
