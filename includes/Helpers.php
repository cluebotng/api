<?PHP

namespace ApiInterface;

function NamespaceNameFromId($ns_id)
{
    $namespaces = array(
        -1 => 'special',
        -2 => 'media',
        0 => 'main',
        1 => 'talk',
        2 => 'user',
        3 => 'user talk',
        4 => 'wikipedia',
        5 => 'wikipedia talk',
        6 => 'file',
        7 => 'file talk',
        8 => 'mediawiki',
        9 => 'mediawiki talk',
        10 => 'template',
        11 => 'template talk',
        12 => 'help',
        13 => 'help talk',
        14 => 'category',
        15 => 'category talk',
        100 => 'portal',
        101 => 'portal talk',
        108 => 'book',
        109 => 'book talk',
        118 => 'draft',
        119 => 'draft talk',
        710 => 'timedtext',
        711 => 'timedtext talk',
        828 => 'module',
        829 => 'module talk',
        2300 => 'gadget',
        2301 => 'gadget talk',
        2302 => 'gadget definition',
        2303 => 'gadget definition talk',
    );
    if (isset($namespaces[$ns_id])) {
        return $namespaces[$ns_id];
    }
}
