<?php
	extract($_POST);
	update_option('rss_feeds', $feed_info);
	
	if($feed_name){
		$feeds = get_option('rss_feeds');
		
		$content = file_get_contents($feed_url);
		$xml = simplexml_load_string($content);
		$xml = json_encode($xml);
		$xml = json_decode($xml,true);
		
		foreach($xml['channel']['item'] as $a){
			$post = array(
					'post_title'=>$a['title'],
					'post_content'=>$a['description'],
					'post_author'=>1,
					'post_status'=>'publish',
					'post_type'=>'feeds'
				);
			$id = wp_insert_post($post);
			wp_set_post_categories($id, array($feed_category));
		}
		
		if($feeds){
			update_option('rss_feeds',$feeds+'&'.$feed_name.'|'.$feed_url.'|'.$feed_category);
		} else {
			update_option('rss_feeds',$feed_name.'|'.$feed_url.'|'.$feed_category);
		}
		$feeds = get_option('rss_feeds');
	} else {
		$feeds = get_option('rss_feeds');
	}
?>
<div>
	<h1>RSS Feed Importer</h1>
	<form action="" method="POST">
		<div>
			<div>Feed Name</div>
			<div>
				<input type="text" name="feed_name" value="<?php echo $feed_name; ?>"/>
			</div>
		</div>
		<div>
			<div>Feed Category</div>
			<div>
				<input type="text" name="feed_category" value="<?php echo $feed_category; ?>"/>
			</div>
		</div>
		<div>
			<div>Feed URL</div>
			<div>
				<input type="text" name="feed_url" value="<?php echo $feed_url; ?>"/>
			</div>
		</div>
		<div>
			<div>Full-Content</div>
			<div>
				<input type="checkbox" name="full_content"/>
			</div>
		</div>
		<div>
			<div>Content Div</div>
			<div>
				<input type="text" name="feed_content" value="<?php echo $feed_content; ?>"/>
			</div>
		</div>
		<input type="hidden" name="feed_info" value="<?php echo $feeds; ?>"/>
		<div>
			<div>
				<input type="submit" name="submit" value="submit"/>
			</div>
		</div>
		<style>
			.header{
				clear: both;
			}
			.header > div{
				float: left;
				width: 200px;
			}
			.layout{
				clear: both;
			}
			.row{
				clear: both;
			}
			.row > div{
				float: left;
				width: 200px;
			}
		</style>
		<div class="header">
			<div>Title</div>
			<div>URL</div>
			<div>Category</div>
			<div>Action</div>
		</div>
		<div class="layout">
			<?php $c = explode('&', $feeds); foreach($c as $a=>$b){ 
				$g = explode('|', $b);
			?>
			<div class="row" data-info="<?php echo $b; ?>">
				<div><?php echo $g[0]; ?></div>
				<div><?php echo $g[1]; ?></div>
				<div><?php echo $g[2]; ?></div>
				<div><a href="">Remove</a></div>
			</div>
			<?php } ?>
		</div>
	</form>
</div>
<script>
	function removeElement(){
		document.getElementsByTagName('input')[5].value = document.getElementsByTagName('input')[5].value.replace(this.parentNode.parentNode.getAttribute('data-info'),'');
		var a = this.parentNode.parentNode.getElementsByTagName('div');
		for(var i = 0; i < a.length; ++i){
			a[i].style.textDecoration = 'line-through';
		}
		return false;
	}

	var info = document.getElementsByClassName('row');
	for(var i = 0; i < info.length; ++i){
		info[i].getElementsByTagName('a')[0].onclick = removeElement;
	}
</script>