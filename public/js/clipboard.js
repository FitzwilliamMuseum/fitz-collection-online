const bootstrapCopy = document.getElementById('bootstrapCopy')
const data = document.getElementById('bootstrapCode')
const wikiCopy = document.getElementById('wikiCopy')
const wikiData = document.getElementById('wikiData')
const harvardCopy = document.getElementById('harvardCopy')
const harvardData = document.getElementById('harvardData')
const apiCopy = document.getElementById('apiCopy')
const apiCode = document.getElementById('apiCode')
const colorCopy = document.getElementById('colorCopy')
const colorsToCopy = document.getElementById('colorsToCopy')

console.log(bootstrapCopy)

function decodeHtml(html) {
    let txt = document.createElement("textarea");
    txt.innerHTML = html;
    return txt.value;
}
if (typeof bootstrapCopy === 'undefined' || bootstrapCopy != null) {
    bootstrapCopy.onclick = () => {
        const text = data.innerHTML.trim().replace(/\n[ ]*/g, "");
        navigator.clipboard.writeText(decodeHtml(text));
    }
}
if (typeof wikiCopy === 'undefined' || wikiCopy != null) {
    wikiCopy.onclick = () => {
        const wikiText = wikiData.innerHTML;
        navigator.clipboard.writeText(decodeHtml(wikiText));
    }
}
if (typeof apiCopy === 'undefined' || apiCopy != null) {
    apiCopy.onclick = () => {
        const apiText = apiCode.innerHTML;
        navigator.clipboard.writeText(decodeHtml(apiText));
    }
}
if (typeof harvardCopy === 'undefined' || harvardCopy != null) {
    harvardCopy.onclick = () => {
        const harvard = harvardData.innerHTML.replace(/>\s+</g, '');
        navigator.clipboard.writeText(decodeHtml(harvard).replace(/>\s+</g, ''));
    }
}
if (typeof colorCopy === 'undefined' || colorCopy != null) {
    colorCopy.onclick = () => {
        const colors = colorsToCopy.innerHTML;
        navigator.clipboard.writeText(decodeHtml(colors));
    }
}
