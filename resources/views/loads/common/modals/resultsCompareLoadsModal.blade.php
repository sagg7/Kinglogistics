<div class="modal fade" id="resultsCompareLoadsModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content" style="max-height: calc(100vh - 3.5rem);">
            <div class="modal-header">
                <h5 class="modal-title" id="">Result Of Compared Loads</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                            
        
                <div class="form-content">
                    
                                <div class="text-center table-responsive" style="height:355px ">
                                    <h3>Compared Loads</h3>
                    
                                    <table class="table table-striped table-bordered mt-1" id="">
                                        <thead>
                                        <tr>
                                            <th>Not In Our System</th>
                                            <th>Matched</th>
                                            <th>In Our System</th>
                                        </tr>
                                        </thead>
                    
                                        <tbody >
                                        <tr >
                                            <td id="externalColumn"></td>
                                            <td id="matchedColumn"></td>
                                            <td id="internalColumn"></td>
                                        </tr>
                                        <tr><td></td></tr>
                                        <tr style="background-color:rgb(255 255 255)">
                                        <td> <button id="buttonDownloadExternal" type="button" class="btn btn-primary">Download Externals Loads</button></td>
                                        <td></td>
                                        <td><button id="buttonDownloadInternal" type="button" class="btn btn-primary">Download Internal Loads</button></td>
                                        </tr>
                                       <tr style="background-color:rgb(255 255 255)">
                                        <td><button id="createLoadsFromExternal" type="button" class="btn btn-primary"> Create Externals Loads </button></td>
                                       </tr>
                                        </tbody>
                                    </table>
                    
                                </div>




                    
                </div>

            </div>

        </div>
    </div>
</div>