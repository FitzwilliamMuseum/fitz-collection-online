<div class="col-md-4">
  <h3 class="lead collection">Relative size of this object</h3>
  <div>
      {!! $comparison !!}
      <a data-bs-toggle="collapse" href="#t-ball" class="btn btn-dark btn-sm mb-1">@svg('fas-question-circle',['width'=>'15', 'class' => 'my-2']) What does this represent?</a>
      <p class="collapse my-3 pt-2" id="t-ball">
        Relative size of this object is displayed using code inspired by
        <a href="https://goodformandspectacle.com/">Good Form and Spectacle</a>'s
        work on the <a href="https://wb.britishmuseum.org">British Museum's Waddeson Bequest website</a> and their
        <a href="https://github.com/goodformandspectacle/dimension-drawer">dimension drawer</a>.
        They chose a tennis ball to represent a universally sized object, from which you
        could envisage the size of an object.
      </p>
  </div>
</div>
