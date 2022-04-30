<div class="container-fluid bg-grey pb-3">

    <div class="container ">
        <h3 class="lead shadow-sm p-3 mx-auto mb-3 mt-3 rounded">
            <a data-bs-toggle="collapse" href="#cite" role="button" aria-expanded="false" aria-controls="cite">
                Cite this record</a>@include('includes.elements.expander-cite')
        </h3>

        <div class="shadow-sm p-3 mx-auto mb-3 mt-3 rounded collapse" id="cite">
            <div class="container">
                @include('includes/elements/citation')
            </div>
        </div>
    </div>

    <div class="container">
        <h3 class="lead shadow-sm p-3 mx-auto  mt-3 rounded">
            <a data-bs-toggle="collapse" href="#formats" role="button" aria-expanded="false" aria-controls="formats">
                Machine readable data</a> @include('includes.elements.expander-formats')
        </h3>
        <div class="shadow-sm p-3 mx-auto mt-3 rounded collapse" id="formats">
            <div class="container">
                @include('includes/elements/formats')
            </div>
        </div>
    </div>
    @include('includes/elements/error')

</div>
