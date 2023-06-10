const tags = document.querySelectorAll('a.tag');

if (tags) {
    tags.forEach(tag => {
        let asciiSum = [...tag.innerText]
            .map(char => char.charCodeAt(0))
            .reduce((current, previous) => previous + current);
        asciiSum = (asciiSum ** 9/4).toString(16);
        if (asciiSum.length > 6) {
            asciiSum = asciiSum.slice(0,6)
        }
        if (asciiSum.length < 6) {
            while (asciiSum.length < 6) {
                asciiSum += 'A';
            }
        }
        tag.style.backgroundColor = `#${asciiSum}`;
        tag.style.color = invertColor(asciiSum, true);

    })

}

// https://stackoverflow.com/questions/35969656/how-can-i-generate-the-opposite-color-according-to-current-color
function invertColor(hex, bw) {
    if (hex.indexOf('#') === 0) {
        hex = hex.slice(1);
    }
    // convert 3-digit hex to 6-digits.
    if (hex.length === 3) {
        hex = hex[0] + hex[0] + hex[1] + hex[1] + hex[2] + hex[2];
    }
    if (hex.length !== 6) {
        throw new Error('Invalid HEX color.');
    }
    let r = parseInt(hex.slice(0, 2), 16),
        g = parseInt(hex.slice(2, 4), 16),
        b = parseInt(hex.slice(4, 6), 16);
    if (bw) {
        // https://stackoverflow.com/a/3943023/112731
        return (r * 0.299 + g * 0.587 + b * 0.114) > 186
            ? '#000000'
            : '#FFFFFF';
    }
    // invert color components
    r = (255 - r).toString(16);
    g = (255 - g).toString(16);
    b = (255 - b).toString(16);
    // pad each with zeros and return
    return "#" + padZero(r) + padZero(g) + padZero(b);
}

function padZero(str, len) {
    len = len || 2;
    const zeros = new Array(len).join('0');
    return (zeros + str).slice(-len);
}
