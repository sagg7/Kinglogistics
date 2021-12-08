<div class="modal fade" id="infoModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
        <div class="modal-content" style="max-height: calc(100vh - 3.5rem);">
            <div class="modal-header">
                <h5 class="modal-title">Guide for advanced template paperwork</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                The following syntax elements are available to create the form template:<br><br>
                <strong>1) {{"text":"Placeholder text","optional"}} </strong><br><br>
                Show a text input with a placeholder text of the data that is being requested to be filled. If the
                property "optional" is present, the input field becomes an optional data to be filled, this property
                also works for any of the following cases.<br><br>
                <strong>2) {{"text":"Placeholder text","answers":["First answer", "Second answer", "Third answer"], "validate"}} </strong><br><br>
                Show a multiple choice question in which the placeholder would serve as a question. In the answers section,
                in case the "validate" property is present, the first answer would represent the correct option
                and all of them would show up randomized each time, at the end all the questions with this property
                will be validated and if the average is above 70. In the other case when this property is not present
                the question would represent a simple multiple choice scenario when the answers appear in the order they
                were provided.<br><br>
                <strong>3) {{"signature"}}</strong><br><br>
                Show an input in which it will be requested the signature of the individual filling the form, if this is
                requested more than once, the next time it will be shown as a checkbox to confirm signing with the first
                signature that was previously filled.<br><br>
                <strong>4) {{"carrier":"name"}} {{"carrier":"address"}} {{"carrier":"phone"}}</strong><br><br>
                A text input in which the carrier data will be requested but it will already be shown filled if this data is
                available and has been previously been filled for the current carrier.<br><br>
                <strong>5) {{"driver":"name"}} {{"driver":"address"}} {{"driver":"phone"}}</strong><br><br>
                A text input in which the driver data will be requested but it will already be shown filled if this data is
                available and has been previously been filled for the current driver.<br><br>
                <strong>6) {{"company":"name"}} {{"company":"address"}} {{"company":"phone"}} {{"company":"signature"}}</strong><br><br>
                For the similar cases of the previous example it will show a text input filled with the company data that
                is available through the company profile section. For the case of "signature" an image of the signature
                saved in the company profile section will be shown, in case there's no signature available the text
                "No company signature available" will appear instead.<br><br>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary btn-block mr-1 mb-1 waves-effect waves-light" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
