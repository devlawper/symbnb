$('#add-image').click(function () {
	// Je récupère ne num des futurs champs que je vais créer
	const index = +$('#widgets-counter').val();

	// Je récupère le prototype des entrées
	const tmpl = $('#ad_images').data('prototype').replace(/__name__/g, index);

	// J'injecte ce code au sein de la div
	$('#ad_images').append(tmpl);

	$('#widgets-counter').val(index + 1);

	// Je gère le bouton supprimer
	handleDeleteButton();
});

function handleDeleteButton() {
	$('button[data-action="delete"]').click(function () {
		const target = this.dataset.target;

		$(target).remove();
	})
}

function updateCounter() {
	const count = +$('#ad_images div.form-group').length;

	$('#widgets-counter').val(count);
}

updateCounter();

handleDeleteButton();