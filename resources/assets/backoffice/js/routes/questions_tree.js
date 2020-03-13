$(function(){
  // Tree

  $('#topic_tree').jstree({
    'core' : {
      'data': goteoData.tree
    }
  });

  $('#topic_tree').bind('select_node.jstree', function (e, data) {
    var anchorText = data.node.text;
    var hrefAttr = anchorText.match(/(?:href=")([^"]+)(?:")/gi);

    if(hrefAttr && (typeof hrefAttr[0] === 'string')) {
      window.open(hrefAttr[0].replace('href="', '').replace('"', ''));
    }
  });
});
