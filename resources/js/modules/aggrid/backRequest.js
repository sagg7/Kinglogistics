function OptionsRenderer() {}

OptionsRenderer.prototype.guidGenerator = () => {
    let S4 = function() {
        return (((1+Math.random())*0x10000)|0).toString(16).substring(1);
    };
    return (S4()+S4()+"-"+S4()+"-"+S4()+"-"+S4()+"-"+S4()+S4()+S4());
}

OptionsRenderer.prototype.confirmationFunction = (route, table, data) => {
    Swal.fire({
        title: data.title,
        showCancelButton: true,
        confirmButtonText: 'Confirm',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#7367F0',
        cancelButtonColor: '#EA5455',
        allowOutsideClick: () => !Swal.isLoading()
    }).then((result) => {
        if (result.value)
            $.ajax({
                url: route,
                type: 'POST',
                success: (res) => {
                    if (!res.success)
                        throwErrorMsg(res.msg);
                    else
                        window[table].updateSearchQuery();
                },
                error: () => {
                    throwErrorMsg();
                },
            });
    });
}

OptionsRenderer.prototype.deleteFunction = (route, table) => {
    OptionsRenderer.prototype.confirmationFunction(route, table, {title: 'Confirmation to delete item'});
}

OptionsRenderer.prototype.init = (params) => {
    this.eGui = document.createElement('div');
    let popId = OptionsRenderer.prototype.guidGenerator(),
        menu = `<button class="btn waves-effect waves-light" data-toggle="popover" data-placement="top" data-container="body" id="pop-${popId}"><i class="fa fa-bars"></i></button>`,
        content = `<ul class="list-group list-group-flush">`,
        menuData = {};

    params.colDef.menuData.forEach((item) => {
        if (item.type) {
            let classId = '',
                icon = '',
                text = '',
                show = true;
            switch (item.type) {
                case 'delete':
                    classId = 'delete';
                    text = 'Delete';
                    icon = 'feather icon-trash-2';
                    break;
                case 'confirm':
                    classId = 'confirm';
                    text = item.text;
                    icon = item.icon;
                    break;
                case 'dynamic':
                    classId = '';
                    text = item.text;
                    icon = item.icon;
                    let condition = "params.data."+item.contdition;
                    if (!eval(condition)){
                        show = false;
                    }
            }
            menuData = item.menuData;
            if (show)
                content += `<li class="list-group-item p-0"><a class="btn-link d-block p-1 ${classId}" href="${item.route}/${params.data.id}"><i class="${icon}"></i> ${text}</a></li>`;
        } else if (item.modal)
            content += `<li class="list-group-item p-0"><a class="btn-link d-block p-1 open-modal" href="${item.route}/${params.data.id}"><i class="${item.icon}"></i> ${item.text}</a></li>`;
        else
            content += `<li class="list-group-item p-0"><a class="btn-link d-block p-1" href="${item.route}/${params.data.id}"><i class="${item.icon}"></i> ${item.text}</a></li>`;
    });

    content += `</ul>`;

    this.eGui.innerHTML = menu;
    $(function () {
        $(`#pop-${popId}`).popover({
            html: true,
            content,
            trigger: 'focus',
        }).on('shown.bs.popover', function (e) {
            let pop = $('.popover'),
                del = pop.find('.delete'),
                confirm = pop.find('.confirm'),
                modal = pop.find('.open-modal');
            modal.click((e) => {
                let btn = $(e.currentTarget),
                    href = btn.attr('href').split("/"),
                    modal = href[0],
                    id = href[1];
                params.api.gridCore.gridOptions.components.OptionModalFunc(modal, id);
            });
            del.click(function (e) {
                e.preventDefault();
                OptionsRenderer.prototype.deleteFunction(del.attr('href'), params.api.gridCore.gridOptions.components.tableRef);
            });
            confirm.click((e) => {
                e.preventDefault();
                OptionsRenderer.prototype.confirmationFunction(confirm.attr('href'), params.api.gridCore.gridOptions.components.tableRef, menuData);
            });
        });
    });
}

OptionsRenderer.prototype.getGui = () => {
    return this.eGui;
}
class DataSource {
    constructor(data) {
        this.url = data.url;
        this.params = data.params;
        this.ajax = null;
        this.successCallback = data.successCallback;
        this.data = null;
    }

    getRows(params)  {
        let data = params.request;
        if (this.params) {
            let searchable = [];
            params.columnApi.columnController.columnDefs.forEach((item) => {
                if (item.filter !== false)
                    searchable.push(item.field);
            });
            data = {...data, ...this.params, ...{searchable}};
        }
        this.ajax = $.ajax({
            url: this.url,
            data,
            type: 'GET',
            beforeSend: () => {
                if (this.ajax !== null)
                    this.ajax.abort();
            },
            success: (res) => {
                // call the success callback
                params.successCallback(res.rows, res.lastRow);
                if (this.successCallback)
                    this.successCallback(res);
                this.data = res;
            },
            error: () => {
                // inform the grid request failed
                params.failCallback();
            },
            always: () => {
                this.ajax = null;
            }
        })
    }
}
class tableAG {
    constructor(properties) {
        this.columns = properties.columns;
        this.columnDefs = [];
        this.renderSimple = typeof properties.renderSimple !== "undefined" ? properties.renderSimple : false;
        this.successCallback = typeof properties.successCallback !== "undefined" ? properties.successCallback : null;
        this.menu = properties.menu ? properties.menu : [];
        this.constructColumns();
        this.page = 0;
        this.dataSource = null;
        let columnDefs = this.columnDefs;
        this.gridOptions = {
            columnDefs,
            rowSelection: "multiple",
            pagination: true,
            paginationPageSize: 15,
            cacheBlockSize: 30,
            rowModelType: 'serverSide',
            pivotPanelShow: "always",
            colResizeDefault: "shift",
            animateRows: true,
            components: {
                OptionsRenderer: OptionsRenderer,
                OptionModalFunc: null,
                tableRef: properties.tableRef,
            },
            defaultColDef: {
                flex: 1,
                minWidth: 100,
                sortable: true,
                resizable: true,
                menuTabs: ['filterMenuTab'],
            },
            //localeText: this.constructLocale(),
            onGridReady: function (params) {
                setTimeout(() => {
                    params.api.sizeColumnsToFit();
                }, 300)

                let rtime,
                    timeout = false,
                    delta = 450;

                window.addEventListener('resize', function () {
                    rtime = new Date();
                    if (timeout === false) {
                        timeout = true;
                        setTimeout(resizeEnd, delta);
                    }
                });

                function resizeEnd() {
                    if (new Date() - rtime < delta) {
                        setTimeout(resizeEnd, delta);
                    } else {
                        timeout = false;
                        params.api.sizeColumnsToFit();
                    }
                }

                if (properties.autoHeight)
                    params.api.setDomLayout('autoHeight');
            },
        };
        if (typeof properties.gridOptions === 'object' && properties.gridOptions !== null)
            _.merge(this.gridOptions, properties.gridOptions);
        this.container = properties.container;
        this.url = properties.url;
        this.pinned = properties.pinned;
        this.init();
    }

    constructColumns() {
        this.columns.forEach((item, i) => {
            let baseProp = item;
            if (item.filter !== false) {
                baseProp = {
                    ...{
                        filter: 'agTextColumnFilter',
                        filterParams: {
                            resetButton: true,
                            debounceMs: 1000,
                            suppressAndOrCondition: true,
                        },
                    },
                    ...item
                };
            }

            this.columnDefs.push(baseProp);
        });
        if (!this.renderSimple && this.menu.length > 0)
            this.columnDefs.push({headerName: 'Menu', width: 50, field: 'options', filter: false, sortable: false, cellRenderer: 'OptionsRenderer', menuData: this.menu});
    }

    constructHtml() {
        let html = `<section>`;
        if (!this.renderSimple)
            html += `<div class="row">` +
                `<div class="col-12 p-0">` +
                `<div class="ag-grid-btns d-flex justify-content-between flex-wrap mb-1">` +
                `<div class="dropdown sort-dropdown mb-1 mb-sm-0">` +
                `<button class="btn btn-white dropdown-toggle border text-dark filter-btn" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">` +
                `Show ${this.gridOptions.paginationPageSize}` +
                `</button>` +
                `<div class="dropdown-menu dropdown-menu-right">` +
                `<a class="dropdown-item" href="#">${this.gridOptions.paginationPageSize}</a>` +
                `<a class="dropdown-item" href="#">50</a>` +
                `<a class="dropdown-item" href="#">100</a>` +
                `<a class="dropdown-item" href="#">150</a>` +
                `</div>` +
                `</div>` +
                `<div class="ag-btns d-flex flex-wrap">` +
                `<div class="input-group">` +
                `<input type="text" class="ag-grid-filter form-control mb-1 mb-sm-0" placeholder="Search...." />` +
                `<div class="input-group-append"><button class="btn btn-primary pl-1 pr-1 waves-effect waves-light" type="button"><i class="fa fa-search"></i></button></div>` +
                `</div>` +
                /*`<div class="btn-export">` +
                `<button class="btn btn-primary ag-grid-export-btn">` +
                `Export as CSV` +
                `</button>` +
                `</div>` +*/
                `</div>` +
                `</div>` +
                `</div>`;
        html += `<div id="${this.container}" class="aggrid ag-theme-material w-100"` +
            `</section>`;
        return html;
    }

    /*constructLocale() {
        return {
            // for filter panel
            page: 'Página',
            more: 'Más',
            to: 'a',
            of: 'de',
            next: 'Siguente',
            last: 'Último',
            first: 'Primero',
            previous: 'Anterior',
            loadingOoo: 'Cargando...',

            // for number filter and text filter
            filterOoo: 'Filtro...',
            equals: 'Igual',
            notEqual: 'Desigual',

            // for text filter
            contains: 'Contiene',
            notContains: 'No contiene',
            startsWith: 'Empieza con',
            endsWith: 'Termina con',

            // filter buttons
            applyFilter: 'Aplicar',
            resetFilter: 'Restablecer filtro',
            clearFilter: 'Limpiar',

        }
    }*/

    updateSearchQuery(val) {
        let dsPar = {url: this.url};
        if (val !== '')
            dsPar.params = {search: val};
        let ds = new DataSource(dsPar);
        this.dataSource = ds;
        this.gridOptions.api.setServerSideDatasource(ds);
    }

    changePageSize(value) {
        this.gridOptions.api.paginationSetPageSize(Number(value));
        this.gridOptions.api.sizeColumnsToFit();
    }

    init() {
        let container = document.querySelector(`#${this.container}`);

        container.id = '';
        container.innerHTML = this.constructHtml();

        let gridTable = document.querySelector(`#${this.container}`);

        const section = $(`#${this.container}`).closest('section');

        /*** FILTER TABLE ***/
        let filter = section.find(`.ag-grid-filter`);
        filter.on("keyup", (e) => {
            if (e.which === 13)
                this.updateSearchQuery(filter.val());
        });
        filter.parent().find("button").click(() => {
            this.updateSearchQuery(filter.val());
        });

        /*** CHANGE DATA PER PAGE ***/
        section.find(`.sort-dropdown .dropdown-item`).on("click", (e) => {
            let el = $(e.target);
            this.changePageSize(el.text());
            section.find(`.filter-btn`).text("Mostrar " + el.text() /*+ " of 500"*/);
        });

        /*** EXPORT AS CSV BTN ***/
        $(".ag-grid-export-btn").on("click", (params) => {
            this.gridOptions.api.exportDataAsCsv();
        });

        /*** INIT TABLE ***/
        new agGrid.Grid(gridTable, this.gridOptions);
        this.gridOptions.api.sizeColumnsToFit();

        /*** SET OR REMOVE PINNED DEPENDING ON DEVICE SIZE ***/
        if ($(window).width() < 768)
            this.gridOptions.columnApi.setColumnPinned(this.pinned, null);
        else
            this.gridOptions.columnApi.setColumnPinned(this.pinned, "left")

        $(window).on("resize", () => {
            if ($(window).width() < 768)
                this.gridOptions.columnApi.setColumnPinned(this.pinned, null);
            else
                this.gridOptions.columnApi.setColumnPinned(this.pinned, "left");
        });

        let ds = new DataSource({url: this.url, successCallback: this.successCallback});
        this.dataSource = ds;
        this.gridOptions.api.setServerSideDatasource(ds);
    }
}
