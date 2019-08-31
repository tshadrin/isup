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
                    e.classList.add(fadeout_class);
                    e.addEventListener('transitionend', function () {
                        this.style.display = "none";
                    });
                });
            }
        }, timeout);
    }
}

export { FadeOut };