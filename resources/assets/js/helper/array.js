Array.prototype.where = function (prop, value = null) {
    return this.filter(function (el) {
        if (value !== null) {
            return el[prop] == value;
        }

        return el == prop;
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

Array.prototype.whereNull = function (prop) {
    return this.filter(function (el) {
        return el[prop] === null;
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
    return this.filter(x => !arr.includes(x));
}

Array.prototype.moveElementUp = function (el) {
    var i = this.indexOf(el);
    if (i > 0) {
        [this[i], this[i-1]] = [this[i-1], this[i]];
    }

    return this;
}

Array.prototype.moveElementDown = function (el) {
    var i = this.indexOf(el);
    if (i < this.length - 1) {
        [this[i], this[i+1]] = [this[i+1], this[i]];
    }
    
    return this;
}

Array.prototype.insert = function (el) {
    this.push(el);
    return this;
}

Array.prototype.removeAtIndex = function (index) {
    this.splice(index, 1);
    return this;
}