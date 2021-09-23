function RemoveBtnRenderer() {}
RemoveBtnRenderer.prototype.deleteFunction = (grid, id, type) => {
    _.pull(window[grid].rowData, _.find(window[grid].rowData, {id, type}));
    // Save the new data into the table
    window[grid].gridOptions.api.setRowData(window[grid].rowData);
    // Set the new rows for total information
    window[grid].gridOptions.api.setPinnedBottomRowData(window[grid].pinnedBottomFunction(window[grid]));

    if (window[grid].onRemoveRow)
        window[grid].onRemoveRow({id, type});
}
RemoveBtnRenderer.prototype.init = (params) => {
    if (params.value !== false) {
        this.eGui = document.createElement('span');
        this.eGui.className = 'd-inline-block';
        this.eGui.innerHTML = `<button type="button" class="btn btn-link btn-remove"><i class="feather icon-x"></i></button>`;

        $(this.eGui).click(function (e) {
            e.preventDefault();
            RemoveBtnRenderer.prototype.deleteFunction(params.api.gridCore.gridOptions.components.tableRef, params.data.id, params.data.type);
        });
    } else {
        this.eGui = null;
    }
}
RemoveBtnRenderer.prototype.getGui = () => {
    return this.eGui;
}
