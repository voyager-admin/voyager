Array.prototype.where = function (prop, value = null) {
    return this.filter(function (el) {
        if (value !== null) {
            return el[prop] == value;
        }

        return el !== prop;
    });
}

Array.prototype.whereNot = function (prop, value = null) {
    return this.filter(function (el) {
        if (value !== null) {
            return el[prop] !== value;
        }

        return el !== prop;
    });
}

Array.prototype.whereLike = function (query, prop = null) {
    return this.filter(function (el) {
        if (prop !== null) {
            return el[prop].toLowerCase().includes(query.toLowerCase());
        } else {
            return el.toLowerCase().includes(query.toLowerCase());
        }
    });
}