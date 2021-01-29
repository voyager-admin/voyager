export default function (input: string, allowed: string): boolean {
    if (allowed == '*/*') {
        return true;
    }

    input = input.toLowerCase();
    allowed = allowed.toLowerCase();

    var parts = allowed.split('/');
    var type = parts[0];
    var subtype = parts[1];

    if (subtype == '*') {
        if (input.startsWith(type+'/')) {
            return true;
        }
    } else if (input == allowed) {
        return true;
    }

    return false;
}