const fs = require ('fs');

var content = 'export default [';
var icons = 0;

fs.readdir(__dirname +'../../../../../node_modules/heroicons/outline', function (err, files) {
    if (err) return;

    files.forEach(function (file) {
        if (file.endsWith('.svg')) {
            file = file.replace('.svg', '');
            content += '\n    "' + file + '",';
            icons++;
        }
    });

    content += '\n];\n';

    fs.writeFile(__dirname +'/../icons.js', content, function (err) {
        if (err) return;
    
        console.log('Icon file generated with '+icons+' icons!');
    });
});