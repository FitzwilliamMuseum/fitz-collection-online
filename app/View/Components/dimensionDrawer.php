<?php

namespace App\View\Components;

use Illuminate\View\Component;

class dimensionDrawer extends Component
{
    public $height;
    public $width;
    public $depth;
    public $viewWidth;
    public $viewHeight;
    public $units;
    public $comparison;
    public $angle = 45;
    public $scale;
    // public $box;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(
      $comparison  = NULL,
      $viewWidth = NULL,
      $viewHeight = NULL,
      $units = NULL,
      $height = NULL,
      $width = NULL,
      $depth = 0.01,
      $scale =  1
      )
    {
        $this->height     = $this->convertToCm($height, $units);
        $this->width      = $this->convertToCm($width, $units);
        $this->depth      = $this->convertToCm($depth, $units);
        $this->units      = $units;
        $this->viewWidth  = $viewWidth;
        $this->viewHeight = $viewHeight;
        $this->scale      = $this->setScale($scale);
        // $this->box        = $this->projectBox();
        $this->comparison = $this->comparison();
    }

    public function setScale($scale)
    {
      $margin = 20;
      # This is an artificial number which approximates perspective.
      $depthScale = 0.5;
      $scaledDepth = $this->depth * $depthScale;
      $totalHeight = $this->height + $this->height_given_angle_and_hyp($this->angle, $scaledDepth);
      $totalWidth = $this->width + $this->width_given_angle_and_hyp($this->angle, $scaledDepth) + 6.7;
      $heightScale = ($this->viewHeight - ($margin * 2)) / $totalHeight;
      $widthScale = ($this->viewWidth - ($margin * 2)) / $totalWidth;
      $calculatedScale = min($heightScale, $widthScale);
      return $calculatedScale;
    }

    public function projectBox(){
      $margin = 20;
      $depthScale = 0.5;
      $scaledDepth = $this->depth * $depthScale;

      $lines = [];
      $lines[] = $this->rect(
        $margin,
        $this->viewHeight - ($margin + ($this->scale * $this->height)),
        $this->scale * $this->width,
        $this->scale *  $this->height
      );
      # first diagonal line
      $lines[] = $this->line(
        $margin,
        ($this->viewHeight - ($margin + ($this->scale * $this->height))),
        $margin + $this->width_given_angle_and_hyp($this->angle, $this->scale * $this->depth * $depthScale),
        ($this->viewHeight - ($margin + ($this->scale * $this->height) + $this->height_given_angle_and_hyp($this->angle, $this->scale * $scaledDepth)))
      );

      # second diagonal line
      $lines[] = $this->line(
        $margin + ($this->scale * $this->width),
        ($this->viewHeight - ($margin + ($this->scale * $this->height))),
        $margin +  ($this->scale * $this->width) + $this->width_given_angle_and_hyp($this->angle, $this->scale * $scaledDepth),
        ($this->viewHeight - ($margin + ($this->scale * $this->height) + $this->height_given_angle_and_hyp($this->angle, $this->scale * $scaledDepth)))
      );

      # third diagonal line
      $lines[] = $this->line(
        $margin + ($this->scale * $this->width),
        ($this->viewHeight - $margin),
        $margin +  ($this->scale * $this->width) +  $this->width_given_angle_and_hyp($this->angle, $this->scale * $scaledDepth),
        ($this->viewHeight - ($margin + $this->height_given_angle_and_hyp($this->angle, $this->scale * $scaledDepth)))
      );

      # top line
      $lines[] = $this->line(
        $margin + $this->width_given_angle_and_hyp($this->angle, $this->scale * $scaledDepth),
        ($this->viewHeight - ($margin + ($this->scale * $this->height) + $this->height_given_angle_and_hyp($this->angle, $this->scale * $scaledDepth))),
        $margin + $this->width_given_angle_and_hyp($this->angle, $this->scale * $scaledDepth) + ($this->scale * $this->width),
        ($this->viewHeight - ($margin + ($this->scale * $this->height) + $this->height_given_angle_and_hyp($this->angle, $this->scale * $scaledDepth))),
      );

      # right line
      $lines[] = $this->line(
        $margin + $this->width_given_angle_and_hyp($this->angle, $this->scale * $scaledDepth) + ($this->scale * $this->width),
        ($this->viewHeight - ($margin + ($this->scale * $this->height) + $this->height_given_angle_and_hyp($this->angle, $this->scale * $scaledDepth))),
        $margin + $this->width_given_angle_and_hyp($this->angle, $this->scale * $scaledDepth) + ($this->scale * $this->width),
        ($this->viewHeight - $margin - $this->height_given_angle_and_hyp($this->angle, $this->scale * $scaledDepth))
      );

      $lines[] = $this->text(
        $margin + ($this->scale * $this->width) / 2,
        $this->viewHeight - $margin - 4,
        $this->measurementLabel($this->width),
        'middle'
      );

      # Height text
      $lines[] = $this->text(
        round($margin + ($this->scale * $this->width) - 2,0),
        round($this->viewHeight - $margin - ($this->scale * $this->height) / 2,0),
        $this->measurementLabel($this->height),
        'end'
      );
      if($this->depth > 0.01){
          # Depth text
          $lines[] = $this->text(
            $margin + ($this->width_given_angle_and_hyp($this->angle, $this->scale * $scaledDepth) / 2) + 20,
            $this->viewHeight - $margin - ($this->scale * $this->height) - ($this->height_given_angle_and_hyp($this->angle, $this->scale * $scaledDepth) / 2),
            $this->measurementLabel($this->depth),
            'start'
        );
      }
      return $lines;
    }

    public function height_given_angle_and_hyp($angle, $hyp)
    {
      $radians = floatval($angle) / 180 * M_PI;
      return floatval($hyp * sin($radians));
    }

    public function width_given_angle_and_hyp($angle, $hyp)
    {
      $radians = floatval($angle) / 180 * M_PI;
      return floatval($hyp) * cos($radians);
    }

    public function line($x1, $y1, $x2, $y2){
      return '<line x1="' . $x1 . '" y1="' . $y1 . '" x2="' . $x2 . '" y2="' . $y2 . '" class="edge"></line>';
    }

    public function rect($x, $y, $width, $height){
      return '<rect x="'. $x . '" y="' . $y .'" width="' . $width . '" height="' . $height .'" class="edge"></rect>';
    }

    public function text($x, $y, $text_content, $text_anchor){
      return '<text x="' . $x .'" y="' . $y .'" text-anchor="' . $text_anchor . '">' . $text_content . '</text>';
    }

    public function convertToCm($dim, $units){
      if($units === 'mm'){
        return $dim * 10;
      } elseif($units === 'm'){
        return $dim * 100;
      } else {
        return $dim;
      }
    }

    public function measurementLabel($dim)
    {
        if($dim < 1) {
          $unit = 'mm';
          $value = $dim * 10;
        } elseif ($dim < 100) {
          $unit = 'cm';
          $value = $dim;
        } else  {
          $unit = 'm';
          $value = $dim / 100;
        }
        return round($value, 1) . ' ' . $unit;
    }

    public function tennisBall($scale, $x, $y){

        $tennis_ball_height = (6.7 * $scale);

        $transformed_scale = $tennis_ball_height / 280;

        return '<g fill-rule="evenodd" transform="translate(' . $x . ',' . $y - $tennis_ball_height .')">
          <g class="tennis-ball" transform="scale('. $transformed_scale . ',' . $transformed_scale . ')">
            <circle class="ball" cx="140.5" cy="140.5" r="139.5"></circle>
            <path class="line" d="M35.4973996,48.6564543 C42.5067217,75.8893541 47.1024057,103.045405 48.5071593,129.267474 C49.2050919,142.295548 49.1487206,156.313997 48.4007524,171.179475 C47.3170518,192.717458 44.831768,215.405368 41.2689042,238.548172 C44.0920595,241.405174 47.0377013,244.140872 50.0973089,246.746747 C54.274085,220.981656 57.1814249,195.664391 58.388118,171.681997 C59.152645,156.487423 59.2103921,142.12682 58.4928407,128.732526 C56.9456805,99.8522041 51.6525537,69.9875212 43.5965239,40.1505937 C40.7799535,42.8710386 38.077622,45.7089492 35.4973996,48.6564543 L35.4973996,48.6564543 Z"></path>
            <path class="line" d="M209.929126,19.4775696 C207.210255,20.7350524 204.523231,22.0798819 201.877774,23.5155872 C185.816543,32.2321125 172.62404,43.5997536 163.365582,57.9858795 C152.309799,75.1647521 147.361062,95.9365435 149.519284,120.438716 C153.246233,162.750546 177.6149,202.948254 215.783496,239.999593 C219.369774,243.480895 223.018502,246.874207 226.714223,250.176799 C229.361836,248.092694 231.93214,245.91478 234.420126,243.648068 C230.467945,240.143617 226.570656,236.534305 222.748767,232.824289 C186.140739,197.287837 162.958794,159.047704 159.480716,119.561284 C157.514766,97.2419721 161.935618,78.6859198 171.774644,63.3976879 C180.045966,50.5454103 191.971382,40.2695847 206.647666,32.3046788 C211.02518,29.9289759 215.539302,27.8153877 220.133919,25.9481492 C216.833521,23.6494818 213.429097,21.4897954 209.929126,19.4775696 L209.929126,19.4775696 Z"></path>
        </g></g>';
    }

    public function comparison()
    {
      $margin = 20;
      return '<svg viewbox="0 0 400 320" class="dimension-view">' .
        implode('', $this->projectBox()) .
        $this->tennisBall($this->scale, $margin + ($this->scale * $this->width) + $margin, $this->viewHeight - $margin) .
      '</svg>';
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.dimension-drawer');
    }
}
