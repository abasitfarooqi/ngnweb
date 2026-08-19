{{-- Shared customer signature pad. Square corners. Same canvas as hire contract termination. --}}
<div id="sigpad" style="width: 100%; height: calc(100vh - 56px); text-align: center;">
    <x-creagia-signature-pad
        class="kbw-signature"
        style="color: white;width:100%; height:100%"
        border-color="#eaeaea"
        pad-classes="rounded-none border-2"
        button-classes="ngn-bg px-4 py-2 mt-4"
        clear-name="Clear"
        submit-name="Submit"
    />
    <button type="button" class="btn btn-danger" data-bs-dismiss="modal" aria-label="Close">Close</button>
</div>
