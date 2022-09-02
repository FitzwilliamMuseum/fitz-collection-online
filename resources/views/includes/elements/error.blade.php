<div class="container ">
    <h3 class="lead shadow-sm p-3 mx-auto mb-3 mt-3 rounded">
        <a data-bs-toggle="collapse" href="#error" role="button" aria-expanded="false" aria-controls="error">
            Contact us</a> @include('includes.elements.expander-error')
    </h3>
    <div class="shadow-sm p-3 mx-auto mt-3 rounded collapse" id="error">
        <div class="container">
            <form class="p-2"
                  action="https://docs.google.com/forms/d/e/1FAIpQLSdxTMfBY8w41N2BDfgad6GnYixACxiFQY-ITcQQfxuCZajcBw/formResponse">
                <div class="form-group">
                    <label for="yourname">Your name</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                @svg('fas-address-book',['width' => 15])
                            </div>
                        </div>
                        <input id="yourname" name="entry.1026021148" placeholder="Please enter your name" type="text"
                               required="required" class="form-control" aria-describedby="yournameHelpBlock">
                    </div>
                    <span id="yournameHelpBlock" class="form-text text-muted">Please enter your name as you would like to be addressed</span>
                </div>
                <div class="form-group">
                    <label for="email">Email address</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                @svg('fas-address-card',['width' => 15])
                            </div>
                        </div>
                        <input id="email" name="entry.1353760488" placeholder="Please enter your email" type="text"
                               required="required" class="form-control" aria-describedby="emailHelpBlock">
                    </div>
                    <span id="emailHelpBlock" class="form-text text-muted">Please enter your email address</span>
                </div>
                <div class="form-group">
                    <label for="object">Object</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                @svg('fas-object-group',['width' => 15])
                            </div>
                        </div>
                        @if(array_key_exists('accession_number', $data['identifier'][0]))
                            <input id="object" value="{{ $data['identifier'][0]['accession_number'] }}"
                                   disabled name="object" placeholder="The object you are commenting on" type="text"
                                   class="form-control" aria-describedby="objectHelpBlock">

                            <input type="hidden" name="entry.467390036"
                                   value="{{ $data['identifier'][0]['accession_number'] }}">
                        @endif
                    </div>
                    <span id="objectHelpBlock" class="form-text text-muted">The object accession number - this is prefilled</span>
                </div>
                <div class="form-group">
                    <label for="query">Query</label>
                    <textarea id="query" name="entry.1755165906" cols="40" rows="5" aria-describedby="queryHelpBlock"
                              required="required" class="form-control"></textarea>
                    <span id="queryHelpBlock" class="form-text text-muted">Please enter your query with as much detail as possible</span>
                </div>
                @if(env('GOOGLE_RECAPTCHA_KEY') != '')
                    <div class="form-group">
                        {!!  GoogleReCaptchaV3::renderField('contact_us_id','contact_us_action') !!}
                    </div>
                @endif
                <div class="form-group">
                    <button name="submit" type="submit" class="btn btn-dark d-block">Submit your query</button>
                </div>
            </form>
        </div>
    </div>
</div>
