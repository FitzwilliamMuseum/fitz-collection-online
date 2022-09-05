<div>
    <a href="{{route('record',[$priref,'format' => 'json'])}}" class="btn btn-dark m-1" aria-label="View this object as a JSON file">JSON</a>
    <a href="{{route('record',[$priref,'format' => 'linked-art'])}}" class="btn btn-dark m-1" aria-label="View this object as a Linked Art JSON file">Linked Art</a>
    <a href="{{route('record',[$priref,'format' => 'xml'])}}" class="btn btn-dark m-1" aria-label="View this object as XML">XML</a>
    <a href="{{route('record',[$priref,'format' => 'txt'])}}" class="btn btn-dark m-1" aria-label="View this object as a text file">TXT</a>
    <a href="{{route('record',[$priref,'format' => 'qr'])}}" class="btn btn-dark m-1" aria-label="Access a QR code for this object">QR code</a>
    <a href="{{route('record',[$priref,'format' => 'mermaid'])}}" class="btn btn-dark m-1" aria-label="View this object as a Mermaid diagram">Mermaid Graph</a>
</div>
