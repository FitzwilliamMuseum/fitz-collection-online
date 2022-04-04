const bootstrapCopy = document.getElementById('bootstrapCopy')
const data = document.getElementById('bootstrapCode')
const wikiCopy = document.getElementById('wikiCopy')
const wikiData = document.getElementById('wikiData')
const harvardCopy = document.getElementById('harvardCopy')
const harvardData = document.getElementById('harvardData')
function decodeHtml(html) {
    let txt = document.createElement("textarea");
    txt.innerHTML = html;
    return txt.value;
}
bootstrapCopy.onclick = () => {
    const text = data.innerHTML;
    navigator.clipboard.writeText(decodeHtml(text));
}
wikiCopy.onclick = () => {
    const wikiText = wikiData.innerHTML;
    navigator.clipboard.writeText(decodeHtml(wikiText));
}
harvardCopy.onclick = () => {
    const harvard = harvardData.innerHTML.replace(/\>\s+\</g,'');
    navigator.clipboard.writeText(decodeHtml(harvard).replace(/\>\s+\</g,''));
}
