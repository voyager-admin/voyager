import * as heroicons from '@heroicons/vue/outline/esm/index';
import Bread from '@/icons/bread';
import Helm from '@/icons/helm';

let icons = [];

icons['Bread'] = Bread;
icons['Helm'] = Helm;

Object.keys(heroicons).filter(x => x != 'default').forEach((name) => {
    icons[name.replace('Icon', '')] = heroicons[name];
});

window.icons = icons;