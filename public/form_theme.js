const addThemeCategoryButton = $('<button type="button" class="btn btn-primary">+ Tilføj</button>');
const wrapper = $('<td colspan="3"></td>').append(addThemeCategoryButton);
const newLinkLi = $('<tr></tr>').append(wrapper);

function addThemeCategoryForm (collectionHolder, $newLinkLi) {
  const prototype = collectionHolder.data('prototype');
  const index = collectionHolder.data('index');
  let newForm = prototype;
  newForm = newForm.replace(/__name__/g, index);
  collectionHolder.data('index', index + 1);
  $newLinkLi.before(newForm);
  addThemeCategoryFormDeleteLink($newLinkLi.prev());
}

function addThemeCategoryFormDeleteLink ($themeCategoryForm) {
  const $removeFormButton = $('<td><button type="button" class="btn btn-danger">- Fjern</button></td>');

  $themeCategoryForm.append($removeFormButton);

  $removeFormButton.on('click', function (e) {
    $themeCategoryForm.remove();
  });
}

jQuery(document).ready(function () {
  collectionHolder = $('tbody.themeCategories');

  collectionHolder.find('tr').each(function () {
    addThemeCategoryFormDeleteLink($(this));
  });

  collectionHolder.append(newLinkLi);

  collectionHolder.data('index', collectionHolder.find(':input').length);

  addThemeCategoryButton.on('click', function (e) {
    addThemeCategoryForm(collectionHolder, newLinkLi);
  });
});
