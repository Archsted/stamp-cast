if (!Array.prototype.find) {
    Array.prototype.find = function(predicate) {
        if (this === null) {
            throw new TypeError('Array.prototype.find called on null or undefined');
        }
        if (typeof predicate !== 'function') {
            throw new TypeError('predicate must be a function');
        }
        let list = Object(this);
        let length = list.length >>> 0;
        let thisArg = arguments[1];
        let value;
        for (let i = 0; i < length; i++) {
            value = list[i];
            if (predicate.call(thisArg, value, i, list)) {
                return value;
            }
        }
        return undefined;
    };
}

if (!Array.prototype.includes) {
    Object.defineProperty(Array.prototype, 'includes', {
        value: function(searchElement, fromIndex) {
            if (this == null) {
                throw new TypeError('"this" is null or not defined');
            }
            let o = Object(this);
            let len = o.length >>> 0;
            if (len === 0) {
                return false;
            }
            let n = fromIndex | 0;
            let k = Math.max(n >= 0 ? n : len - Math.abs(n), 0);
            while (k < len) {
                if (o[k] === searchElement) {
                    return true;
                }
                k++;
            }
            return false;
        }
    });
}

Array.prototype.findIndex = Array.prototype.findIndex || function(callback) {
    if (this === null) {
        throw new TypeError('Array.prototype.findIndex called on null or undefined');
    } else if (typeof callback !== 'function') {
        throw new TypeError('callback must be a function');
    }
    let list = Object(this);
    // Makes sures is always has an positive integer as length.
    let length = list.length >>> 0;
    let thisArg = arguments[1];
    for (let i = 0; i < length; i++) {
        if ( callback.call(thisArg, list[i], i, list) ) {
            return i;
        }
    }
    return -1;
};
