<?php
	$userManager = new UserManager;
	$this->_title = 'Accueil';
?>
<div class="container has-text-centered">
	<?php if (isset($images)): ?>
		<?php foreach($images as $image): ?>
			<div class="columns is-centered">
				<div class="column is-one-third">
					<p class="image is-4by3">
						<img src="<?= $image->pathToImage() ?>"/>
					</p>
					<p id="likes<?= $image->id() ?>" class="is-size-4 has-text-danger has-text-centered has-text-weight-bold">
						<?= $image->likes() ?>
					</p>
					<?php if (isset($_SESSION['logged'])): ?>
						<button class="button" id="like<?= $image->id() ?>" onclick="likeImage(this)">Like</button>
					<?php endif; ?>
				</div>
				<div class="column is-half">
					<?php
						$comments = $image->comments();
						if ($comments):
							foreach($comments as $comment): ?>
								<article class="media has-background-primary" id="image<?= $image->id() ?>comment<?= $comment->id() ?>">
									<div class="media-content">
										<div class="content">
											<p>
												<strong>
													<?php
														$user = $userManager->getUserById($comment->userId())[0];
														echo $user->username();
													?>
												</strong>
												<br/>
												<?= $comment->commentText() ?>
											</p>
										</div>
									</div>
								</article>
							<?php endforeach;
						endif; ?>
					<?php if (isset($_SESSION['logged'])): ?>
						<article class="media">
							<div class="media-content">
								<div class="field">
									<p class="control">
										<textarea class="textarea is-small" id="text<?= $image->id() ?>" placeholder="Raconte ta vie..."></textarea>
									</p>
								</div>
								<div class="field">
									<p class="control">
										<button class="button" id="comment<?= $image->id() ?>" onclick="postComment(this)">Post comment</button>
									</p>
								</div>
							</div>
						</article>
					<?php endif; ?>
				</div>
			</div>
		<?php endforeach; ?>
	<?php else: ?>
		<div>La Gallerie est vide</div>
	<?php endif; ?>
</div>

<script>
	function ajaxify(jsonString) {
		var httpRequest = new XMLHttpRequest();
		httpRequest.onreadystatechange = function() {
			if (httpRequest.readyState === 4 && httpRequest.status !== 200) {
				console.log('error return requete serveur');
				document.write(httpRequest.status);
				return false;
			}
			else if (httpRequest.readyState === 4 && httpRequest.status === 200) {
				var httpResponse = httpRequest.response;
				if (httpResponse) {
					var obj = JSON.parse(httpRequest.response);
				}
				if (obj && 'like' in obj) {
					likeResponse(jsonString, obj);
				}
			}
		}
		httpRequest.open('POST', 'index.php?url=accueil', true);
		httpRequest.setRequestHeader('Content-Type', 'multipart/form-data');
		httpRequest.send(jsonString);
	}

	function likeImage(likeButton) {
		buttonId = likeButton.getAttribute('id');
		imgId = buttonId.match(/\d+/)[0];
		ajaxify(JSON.stringify({ like:1, imageId:imgId }));
	}

	function likeResponse(sentData, responseData) {
		json = JSON.parse(sentData);
		likesNb = document.getElementById('likes' + json['imageId']);
		likesNb.innerHTML = responseData['likes'];
	}

	function addCommentNode(commentText, imgId) {

	}

	function postComment(commentButton) {
		buttonId = commentButton.getAttribute('id');
		imgId = buttonId.match(/\d+/)[0];
		commentText = document.getElementById('text' + imgId).value;
		ajaxify(JSON.stringify({ comment:1, imageId:imgId, commentText:commentText }));
	}
</script>
