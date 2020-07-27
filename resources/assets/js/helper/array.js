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

Array.prototype.shuffle = function () {
    for (let i = this.length - 1; i > 0; i--) {
        const j = Math.floor(Math.random() * (i + 1));
        [this[i], this[j]] = [this[j], this[i]];
    }

    return this;
}

Array.prototype.first = function () {
    return this[0];
}

Array.prototype.pluck = function (prop) {
    return this.map(function (el) {
        return el[prop];
    });
}

Array.prototype.diff = function (arr) {
    return this.filter(x => !arr.includes(x))
}
