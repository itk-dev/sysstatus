// See https://symfony.com/doc/current/form/form_collections.html

var $collectionHolder;

var $addQuestionButton = $('<button type="button" class="btn btn-primary">+ Tilføj</button>');
var wrapper = $('<td colspan="3"></td>').append($addQuestionButton);
var $newLinkLi = $('<tr></tr>').append(wrapper);

function addQuestionForm ($collectionHolder, $newLinkLi) {
  var prototype = $collectionHolder.data('prototype');
  var index = $collectionHolder.data('index');
  var newForm = prototype;
  newForm = newForm.replace(/__name__/g, index);
  $collectionHolder.data('index', index + 1);
  $newLinkLi.before(newForm);
  addQuestionFormDeleteLink($newLinkLi.prev());
}

function addQuestionFormDeleteLink ($questionForm) {
  var $removeFormButton = $('<td><button type="button" class="btn btn-danger">- Fjern</button></td>');

  $questionForm.append($removeFormButton);

  $removeFormButton.on('click', function (e) {
    $questionForm.remove();
  });
}

jQuery(document).ready(function () {
  $collectionHolder = $('tbody.questions');

  $collectionHolder.find('tr').each(function () {
    addQuestionFormDeleteLink($(this));
  });

  $collectionHolder.append($newLinkLi);

  $collectionHolder.data('index', $collectionHolder.find(':input').length);

  $addQuestionButton.on('click', function (e) {
    addQuestionForm($collectionHolder, $newLinkLi);
  });
});
