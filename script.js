const loadMoreButton = document.querySelector('.load-more-button');

function loadMorePosts() {
  const postsRequest = new XMLHttpRequest();

  const containerClass = loadMoreButton.dataset.containerclass;
  const taxonomyField = loadMoreButton.dataset.taxonomyfield;
  const taxonomyName = loadMoreButton.dataset.taxonomyname;
  const taxonomyTerm = loadMoreButton.dataset.taxonomyterm;
  const templatePath = loadMoreButton.dataset.templatepath;
  const loadingText = loadMoreButton.dataset.loadingtext;
  const labelText = loadMoreButton.dataset.labeltext;
  const allPosts = parseInt(loadMoreButton.dataset.allposts, 10);
  const postType = loadMoreButton.dataset.posttype;
  const perLoad = parseInt(loadMoreButton.dataset.perload, 10);
  const orderBy = loadMoreButton.dataset.orderby;
  const offset = parseInt(loadMoreButton.dataset.offset, 10);
  const { search } = loadMoreButton.dataset;
  const { order } = loadMoreButton.dataset;

  loadMoreButton.innerText = loadingText;
  loadMoreButton.classList.add('loading');

  let container = document.querySelector(`.${containerClass}`);

  if (!container) {
    container = document.createElement('div');
    container.classList.add(containerClass);
    const parent = loadMoreButton.parentNode;
    parent.insertBefore(container, loadMoreButton);
  }

  postsRequest.open('POST', '/wp-admin/admin-ajax.php', true);
  postsRequest.setRequestHeader(
    'Content-Type',
    'application/x-www-form-urlencoded;'
  );
  postsRequest.onload = function loadPosts() {
    if (this.status >= 200 && this.status < 400) {
      container.innerHTML += this.response;
      loadMoreButton.dataset.offset = offset + perLoad;
      if (loadMoreButton.dataset.offset >= allPosts) {
        loadMoreButton.remove();
      }
      loadMoreButton.innerText = labelText;
      loadMoreButton.classList.remove('loading');
    }
  };
  postsRequest.onerror = function loadError() {};
  postsRequest.send(
    `action=et_load_more_posts&post_type=${postType}&per_load=${perLoad}&offset=${offset}&template_path=${templatePath}&search=${search}&order=${order}&orderby=${orderBy}&taxonomy_name=${taxonomyName}&taxonomy_field=${taxonomyField}&taxonomy_term=${taxonomyTerm}`
  );
}

if (loadMoreButton) {
  loadMoreButton.addEventListener('click', loadMorePosts);
}
