function OptionsRenderer() {}

OptionsRenderer.prototype.guidGenerator = () => {
    let S4 = function() {
        return (((1+Math.random())*0x10000)|0).toString(16).substring(1);
    };
    return (S4()+S4()+"-"+S4()+"-"+S4()+"-"+S4()+"-"+S4()+S4()+S4());
}

OptionsRenderer.prototype.confirmationFunction = (route, table, data, params = null) => {
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
                    else {
                        if (!data.stopReloadOnConfirm)
                            window[table].updateSearchQuery();
                        if (data.afterConfirmFunction)
                            data.afterConfirmFunction(params);
                    }
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

    params.data.menuId = [];
    params.colDef.menuData.forEach((item, i) => {
        const itemId = OptionsRenderer.prototype.guidGenerator();
        let href = `${item.route}/${params.data.id}`;
        if (item.route_params) {
            href += '?';
            Object.entries(item.route_params).forEach(([key, value]) => {
                href += `${key}=${value}&`;
            });
        }
        params.data.menuId.push({id: itemId, type: item.type});
        if (item.type) {
            let classId = '',
                icon = '',
                text = '';
            if (item.conditional) {
                let condition = "params.data." + item.conditional;
                if (!eval(condition))
                    return;
            }
            switch (item.type) {
                default:
                    classId = '';
                    text = item.text;
                    icon = item.icon;
                    break;
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
            }
            menuData = item.menuData;
            content += `<li class="list-group-item p-0"><a class="btn-link d-block p-1 ${classId}" href="${href}" id="${itemId}"><i class="${icon}"></i> ${text}</a></li>`;
        } else if (item.modal)
            content += `<li class="list-group-item p-0"><a class="btn-link d-block p-1 open-modal" href="${href}" id="${itemId}"><i class="${item.icon}"></i> ${item.text}</a></li>`;
        else
            content += `<li class="list-group-item p-0"><a class="btn-link d-block p-1" href="${href}" id="${itemId}"><i class="${item.icon}"></i> ${item.text}</a></li>`;
    });

    content += `</ul>`;

    this.eGui.innerHTML = menu;
    $(function () {
        $(`#pop-${popId}`).popover({
            html: true,
            content,
            trigger: 'click',
        }).on('shown.bs.popover', (e) => {
            params.colDef.menuData.forEach((item, i) => {
                const menuId = params.data.menuId[i];
                const option = $(`#${menuId.id}`);
                switch (item.type) {
                    default:
                        break;
                    case 'modal':
                        option.click((e) => {
                            let btn = $(e.currentTarget),
                                href = btn.attr('href').split("/"),
                                modal = href[0],
                                id = href[1];
                            params.api.gridCore.gridOptions.components.OptionModalFunc(modal, id);
                        });
                        break;
                    case 'delete':
                        option.click(function (e) {
                            e.preventDefault();
                            OptionsRenderer.prototype.deleteFunction(option.attr('href'), params.api.gridCore.gridOptions.components.tableRef);
                        });
                        break;
                    case 'confirm':
                        option.click((e) => {
                            e.preventDefault();
                            OptionsRenderer.prototype.confirmationFunction(option.attr('href'), params.api.gridCore.gridOptions.components.tableRef, item.menuData, params);
                        });
                        break;
                }
            });
            $('body').on('click', function (e) {
                $('[id^=pop-]').each(function () {
                    // hide any open popovers when the anywhere else in the body is clicked
                    if (!$(this).is(e.target) && $(this).has(e.target).length === 0 && $('.popover').has(e.target).length === 0) {
                        $(this).popover('hide');
                    }
                });
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
        this.formatResult = data.formatResult;
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
                this.data = res;
                if (this.formatResult) {
                    this.data = this.formatResult(res);
                }
                // call the success callback
                params.successCallback(this.data.rows, this.data.lastRow);
                if (this.successCallback)
                    this.successCallback(res);
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
        this.formatDSResult = properties.formatDSResult ? properties.formatDSResult : null;
        this.menu = properties.menu ? properties.menu : [];
        this.constructColumns();
        this.page = 0;
        this.dataSource = null;
        this.searchQueryParams = properties.searchQueryParams ? properties.searchQueryParams : {};
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
                OptionsRenderer,
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
                    params.columnApi.autoSizeAllColumns();
                    params.api.sizeColumnsToFit();
                }, 300)

                let rtime,
                    timeout = false,
                    delta = 550;

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
                        params.columnApi.autoSizeAllColumns();
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

    updateSearchQuery(params = {}) {
        let dsPar = {url: this.url, successCallback: this.successCallback, formatResult: this.formatDSResult};
        if (this.searchQueryParams)
            dsPar.params = _.merge(params, this.searchQueryParams)
        else
            dsPar.params = params;
        let ds = new DataSource(dsPar);
        this.dataSource = ds;
        this.gridOptions.api.setServerSideDatasource(ds);
    }

    changePageSize(value) {
        this.gridOptions.api.paginationSetPageSize(Number(value));
        this.gridOptions.columnApi.autoSizeAllColumns();
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
                this.updateSearchQuery({search: filter.val()});
        });
        filter.parent().find("button").click(() => {
            this.updateSearchQuery({search: filter.val()});
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
        this.gridOptions.columnApi.autoSizeAllColumns();
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

        let ds = new DataSource({url: this.url, successCallback: this.successCallback, params: this.searchQueryParams, formatResult: this.formatDSResult});
        this.dataSource = ds;
        this.gridOptions.api.setServerSideDatasource(ds);
    }
}
