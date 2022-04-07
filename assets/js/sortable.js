import Sortable from 'sortablejs';
import * as bootstrap from 'bootstrap';

const milestones = document.getElementById('project-milestones');
const isGranted = document.getElementById('can-edit-milestones');

if (milestones && isGranted) {
    const milestoneToast = document.getElementById('js-toast');

    new Sortable(milestones, {
        animation: 250,
        ghostClass: 'project-milestone-drag',
        easing: "cubic-bezier(1, 0, 0, 1)",
        sort: 'true' === isGranted.dataset.granted,
        onChoose: function(event) {
            event.item.placeholder = document.createComment('sort-placeholder');
            event.item.after(event.item.placeholder);
        },
        onEnd: async function(event) {
            const item = event.item;
            const url = item.dataset.route;
            const data = new FormData();

            if (event.oldIndex === event.newIndex) {
                return;
            }

            data.append('oldPosition', event.oldIndex);
            data.append('newPosition', event.newIndex);

            const request = await fetch(url, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: data,
            });

            const response = await request.json();

            // Undo the sorting if something went wrong
            if (event.item.placeholder) {
                if (response.status !== 'success') {
                    event.item.placeholder.replaceWith(event.item);
                }

                delete event.item.placeholder;
            }

            if (milestoneToast) {
                const toast = bootstrap.Toast.getOrCreateInstance(milestoneToast);
                const toastMessage = document.getElementById('toast-message');

                toastMessage.innerHTML = response.message;
                milestoneToast.classList.remove('bg-success', 'bg-danger');
                milestoneToast.classList.add(response.status === 'error' ? 'bg-danger' : 'bg-success');

                toast.show();
            }
        },
    });
}
