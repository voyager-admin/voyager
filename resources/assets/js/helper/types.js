window.isArray = function (input)
{
    return (input && typeof input === 'object' && input instanceof Array);
};
Vue.prototype.isArray = isArray;

window.isObject = function (input)
{
    return (input && typeof input === 'object' && input.constructor === Object);
};
Vue.prototype.isObject = isObject;

window.isString = function (input)
{
    return (typeof input === 'string');
};
Vue.prototype.isString = isString;

window.isNumber = function (input)
{
    return (typeof input === 'number');
};
Vue.prototype.isNumber = isNumber;

window.isBoolean = function (input)
{
    return (typeof input === 'boolean');
};
Vue.prototype.isBoolean = isBoolean;

window.mimeMatch = function (mime, match)
{
    var matcher = new this.MimeMatcher(match);
    return matcher.match(mime);
};
Vue.prototype.mimeMatch = mimeMatch;