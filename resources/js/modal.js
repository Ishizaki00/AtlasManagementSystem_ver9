// resources/js/modal.js

// document.addEventListener('DOMContentLoaded', function () {
//     // モーダルを開くボタン
//     const openButtons = document.querySelectorAll('.open-modal-btn');
//     const modal = document.getElementById('editModal');
//     const closeBtn = document.querySelector('.close-modal-btn');

//     openButtons.forEach(button => {
//         button.addEventListener('click', function () {
//             // 必要なら投稿の内容を取得してモーダルに反映する
//             const postContent = this.getAttribute('data-post-content');
//             const postId = this.getAttribute('data-post-id');
//             document.getElementById('editPostContent').value = postContent;
//             document.getElementById('editPostId').value = postId;

//             modal.classList.add('show');
//             modal.style.display = 'block';
//         });
//     });

//     closeBtn.addEventListener('click', function () {
//         modal.classList.remove('show');
//         modal.style.display = 'none';
//     });
// });
document.addEventListener('DOMContentLoaded', function () {
  const editButtons = document.querySelectorAll('.edit-btn');
  editButtons.forEach(button => {
    button.addEventListener('click', function () {
      const postId = this.getAttribute('data-id');
      const postTitle = this.getAttribute('data-title');
      const postBody = this.getAttribute('data-body');

      document.getElementById('edit-post-id').value = postId;
      document.getElementById('edit-post-title').value = postTitle;
      document.getElementById('edit-post-body').value = postBody;
    });
  });
});
