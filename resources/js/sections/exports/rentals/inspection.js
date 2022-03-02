(() => {
    const conditionBg = $('#conditionBackground');
    const conditionCanvas = $('.condition-canvas');
    let conditionJSON,
        conditionCtx;
    const drawConditionFigure = (figure, coords, skipSave) => {
        let ctx = conditionCtx;
        ctx.save();
        ctx.beginPath();
        ctx.fillStyle = "#C80000";
        ctx.strokeStyle = "#C80000";

        switch (figure) {
            case "impact":
                ctx.arc(coords.x, coords.y, 8, 0, 2 * Math.PI);
                ctx.stroke();
                ctx.beginPath();
                ctx.arc(coords.x, coords.y, 8, 0, 2 * Math.PI);
                ctx.fill();
                break;
            case "broken":
                ctx.lineWidth = 2;
                ctx.beginPath();
                ctx.moveTo(coords.x - 7, coords.y - 7);
                ctx.lineTo(coords.x + 7, coords.y + 7);
                ctx.moveTo(coords.x - 7, coords.y + 7);
                ctx.lineTo(coords.x + 7, coords.y - 7);
                ctx.stroke();
                break;
            case "scratch":
                ctx.lineWidth = 2;
                ctx.beginPath();
                ctx.moveTo(coords.x - 8, coords.y + 3);
                ctx.lineTo(coords.x - 2, coords.y - 2);
                ctx.lineTo(coords.x + 2, coords.y + 2);
                ctx.lineTo(coords.x + 8, coords.y - 3);
                ctx.stroke();
                break;
            default:
                return false;
        }

        if (typeof (skipSave) == "undefined" || !skipSave) {
            conditionJSON.push({
                type: figure,
                pos: coords
            });
        }

        ctx.restore();
    }, populateConditionCanvas = () => {
        conditionJSON.forEach(function (element) {
            drawConditionFigure(element.type, element.pos, true);
        }, this);
    }
    let conditionFlag = false;
    let imagesLoaded = false;
    let hasCalledPrint = false;
    const printPage = () => {
        if (conditionFlag && imagesLoaded && !hasCalledPrint) {
            hasCalledPrint = true;
            window.print();
            window.location.href = '/rental/index';
        }
    }
    (() => {
        const carConditionImg = new Image();
        conditionCtx = conditionCanvas[0].getContext('2d');
        conditionJSON = $('#conditionData').val();

        if (conditionJSON === "") {
            conditionJSON = [];
        } else {
            conditionJSON = JSON.parse(conditionJSON);
        }

        carConditionImg.addEventListener('load', () => {
            conditionCtx.drawImage(carConditionImg, 0, 0, 760, 416);
            populateConditionCanvas();
            setTimeout(() => {
                conditionFlag = true;
                printPage();
            }, 300);
        }, false);

        carConditionImg.src = conditionBg.val();
    })();
    Promise.all(Array.from(document.images).filter(img => !img.complete).map(img => new Promise(resolve => {
        img.onload = img.onerror = resolve;
    }))).then(() => {
        $('.masonry_grid').masonry({
            percentPosition: true,
        });
        setTimeout(() => {
            imagesLoaded = true;
            printPage();
        }, 300);
    });
})();
