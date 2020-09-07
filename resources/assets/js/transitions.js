let transitions = [
    'Collapse',
    'Fade',
    'SlideDown',
    'SlideLeft',
    'SlideRight',
    'SlideUp',
];

export default (voyager) => {
    transitions.forEach(function (transition) {
        var name = transition.match(/[A-Z]{2,}(?=[A-Z][a-z]+[0-9]*|\b)|[A-Z]?[a-z]+[0-9]*|[A-Z]|[0-9]+/g)
                            .map(x => x.toLowerCase())
                            .join('-');
        voyager.component(name+'-transition', require('../components/Transitions/'+transition).default);
    });
};