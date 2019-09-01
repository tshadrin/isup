'use strict';

class FadeOut
{
    /**
     * @param block_class
     * @param timeout
     * @param fadeout_class
     */
    static bind(block_class, timeout=3000, fadeout_class='hidden') {
        setTimeout(function() {
            let elements = document.querySelectorAll(block_class);
            if (elements.length > 0 ) {
                elements.forEach(function (e) {
                    FadeOut.bindElement(e, timeout, fadeout_class);
                });
            }
        }, timeout);
    }
    /**
     * @param element
     * @param timeout
     * @param fadeout_class
     */
    static bindElement(element, timeout=3000, fadeout_class='hidden') {
        setTimeout(function() {
            element.classList.add(fadeout_class);
            element.addEventListener('transitionend', function () {
                this.style.display = "none";
            });
        }, timeout);
    }
}

export { FadeOut };