
const addQuestionButton = $('<button type="button" class="btn btn-primary">+ Tilføj</button>');
const wrapper = $('<td colspan="3"></td>').append(addQuestionButton);
const newLinkLi = $('<tr></tr>').append(wrapper);

function addQuestionForm (collectionHolder, $newLinkLi) {
  const prototype = collectionHolder.data('prototype');
  const index = collectionHolder.data('index');
  let newForm = prototype;
  newForm = newForm.replace(/__name__/g, index);
  collectionHolder.data('index', index + 1);
  $newLinkLi.before(newForm);
  addQuestionFormDeleteLink($newLinkLi.prev());
}

function addQuestionFormDeleteLink ($questionForm) {
  const $removeFormButton = $('<td><button type="button" class="btn btn-danger">- Fjern</button></td>');

  $questionForm.append($removeFormButton);

  $removeFormButton.on('click', function (e) {
    $questionForm.remove();
  });
}

jQuery(document).ready(function () {
  collectionHolder = $('tbody.questions');

  collectionHolder.find('tr').each(function () {
    addQuestionFormDeleteLink($(this));
  });

  collectionHolder.append(newLinkLi);

  collectionHolder.data('index', collectionHolder.find(':input').length);

  addQuestionButton.on('click', function (e) {
    addQuestionForm(collectionHolder, newLinkLi);
  });
});
