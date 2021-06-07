(() => {
    const resizeCanvas = (canvas, sigPad) => {
        // When zoomed out to less than 100%, for some very strange reason,
        // some browsers report devicePixelRatio as less than 1
        // and only part of the canvas is cleared then.
        var ratio =  Math.max(window.devicePixelRatio || 1, 1);

        // This part causes the canvas to be cleared
        canvas.width = canvas.offsetWidth * ratio;
        canvas.height = canvas.offsetHeight * ratio;
        canvas.getContext("2d").scale(ratio, ratio);

        // This library does not listen for canvas changes, so after the canvas is automatically
        // cleared by the browser, SignaturePad#isEmpty might still return false, even though the
        // canvas looks empty, because the internal data of this library wasn't cleared. To make sure
        // that the state of this library is consistent with visual state of the canvas, you
        // have to clear it manually.
        sigPad.clear();
    };
    let sigPadArr = [];
    canvases.forEach((item) => {
        const required = item.required ? item.required : false,
            canvas = item.canvas,
            sigPad = new SignaturePad(canvas),
            parent = canvas.parentNode,
            btn = parent.querySelector("button"),
            label = parent.parentNode.querySelector("label").innerText,
            input = document.createElement("input");

        btn.addEventListener("click", (e) => {
            sigPad.clear();
        });

        input.name = canvas.id;
        input.hidden = true;

        canvas.parentNode.insertBefore(input, canvas.nextSibling);

        sigPadArr.push({signaturePad: sigPad, input, required, label});
        window.onresize = () => {
            resizeCanvas(canvas, sigPad);
        };
    });

    $('form').submit((e) => {
        sigPadArr.forEach((obj) => {
            if (obj.required && obj.signaturePad.isEmpty()) {
                throwErrorMsg(`The ${obj.label} is required`);
                e.preventDefault();
            } else
                obj.input.value = obj.signaturePad.toDataURL();
        });
    });
})();
