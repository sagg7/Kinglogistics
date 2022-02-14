(() => {
    const loadSummary = [];
    const summaryArea = $('#loads-summary');
    const summaryTable = summaryArea.find('table');
    const shipper = $('#shipper');
    const getLoadsData = () => {
        summaryTable.find('h2').text(0);
        $.ajax({
            url: '/dashboard/getData',
            type: 'GET',
            data: {
                shipper: shipper.val(),
            },
            success: (res) => {
                if (res.loads) {
                    Object.entries(res.loads).forEach(item => {
                        const [key, value] = item;
                        $(`#${key}_summary`).text(value.count);
                        loadSummary.push({
                            status: key,
                            count: value.count,
                            data: value.data,
                        });
                    });
                }
            },
            error: () => {
                throwErrorMsg();
            }
        });
    };
    getLoadsData();
    $(`[id*="_summary"]`).click((e) => {
        e.preventDefault();
        e.stopPropagation();
        const heading = $(e.currentTarget),
            id = heading.attr('id').split('_summary')[0];
        const status = loadSummary.find(obj => obj.status === id);
        if (status)
            showStatusModal(status);
    });
    shipper.change(() => {
        loadSummary.length = 0;
        getLoadsData();
    });
})();
