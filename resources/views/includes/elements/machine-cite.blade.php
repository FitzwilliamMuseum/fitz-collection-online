<div class="container-fluid bg-grey pb-3">

  <div class="container ">
    <h3 class="lead shadow-sm p-3 mx-auto mb-3 mt-3 rounded">
      <a data-toggle="collapse" href="#cite" role="button" aria-expanded="false" aria-controls="cite">
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
        <a data-toggle="collapse" href="#formats" role="button" aria-expanded="false" aria-controls="formats">
          Machine readable data</a> @include('includes.elements.expander-formats')
        </h3>
        <div class="shadow-sm p-3 mx-auto mt-3 rounded collapse" id="formats">
          <div class="container">
            @include('includes/elements/formats')
          </div>
        </div>
      </div>
      <div class="container ">
        <h3 class="lead shadow-sm p-3 mx-auto mb-3 mt-3 rounded">
          <a data-toggle="collapse" href="#error" role="button" aria-expanded="false" aria-controls="error">
            Contact us</a> @include('includes.elements.expander-error')
          </h3>
          <div class="shadow-sm p-3 mx-auto mt-3 rounded collapse" id="error">
            <div class="container">
              <form class="p-3" action="https://docs.google.com/forms/d/e/1FAIpQLSdxTMfBY8w41N2BDfgad6GnYixACxiFQY-ITcQQfxuCZajcBw/formResponse">
                <div class="form-group row">
                  <label for="yourname" class="col-4 col-form-label">Your name</label>
                  <div class="col-8">
                    <div class="input-group">
                      <div class="input-group-prepend">
                        <div class="input-group-text">
                          <i class="fa fa-address-book"></i>
                        </div>
                      </div>
                      <input id="yourname" name="entry.1026021148" placeholder="Please enter your name" type="text" required="required" class="form-control">
                    </div>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="email" class="col-4 col-form-label">Email address</label>
                  <div class="col-8">
                    <div class="input-group">
                      <div class="input-group-prepend">
                        <div class="input-group-text">
                          <i class="fa fa-address-card"></i>
                        </div>
                      </div>
                      <input id="email" name="entry.1353760488" placeholder="Please enter your email" type="text" class="form-control" required="required">
                    </div>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="object" class="col-4 col-form-label">Object</label>
                  <div class="col-8">
                    <div class="input-group ">
                      <div class="input-group-prepend">
                        <div class="input-group-text">
                          <i class="fa fa-object-group"></i>
                        </div>
                      </div>
                      <input id="object" disabled name="" placeholder="The object you are commenting on" type="text" class="form-control" value="{{ $record['_source']['identifier'][0]['accession_number'] }}">
                      <input type="hidden" name="entry.467390036"  value="{{ $record['_source']['identifier'][0]['accession_number'] }}">
                    </div>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="query" class="col-4 col-form-label">Query</label>
                  <div class="col-8">
                    <textarea id="query" name="entry.1755165906" cols="40" rows="5" class="form-control" aria-describedby="queryHelpBlock" required="required"></textarea>
                    <span id="queryHelpBlock" class="form-text text-muted">Please enter your query with as much detail as possible</span>
                  </div>
                </div>

                <div class="form-group row">
                  <div class="offset-4 col-8">
                    <button name="submit" type="submit" class="btn btn-dark d-block">Submit</button>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
