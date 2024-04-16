<?php
class ModelToolImage extends Model {
	public function resize($filename, $width, $height) {
		if (!is_file(DIR_IMAGE . $filename) || substr(str_replace('\\', '/', realpath(DIR_IMAGE . $filename)), 0, strlen(DIR_IMAGE)) != str_replace('\\', '/', DIR_IMAGE)) {
			return;
		}

		$extension = pathinfo($filename, PATHINFO_EXTENSION);

		$image_old = $filename;
		$image_new = 'cache/' . utf8_substr($filename, 0, utf8_strrpos($filename, '.')) . '-' . (int)$width . 'x' . (int)$height . '.' . $extension;

		if (!is_file(DIR_IMAGE . $image_new) || (filemtime(DIR_IMAGE . $image_old) > filemtime(DIR_IMAGE . $image_new))) {
			list($width_orig, $height_orig, $image_type) = getimagesize(DIR_IMAGE . $image_old);
				 
			if (!in_array($image_type, array(IMAGETYPE_PNG, IMAGETYPE_JPEG, IMAGETYPE_GIF)) && $image_type) { 
				return DIR_IMAGE . $image_old;
			}
						
			$path = '';

			$directories = explode('/', dirname($image_new));

			foreach ($directories as $directory) {
				$path = $path . '/' . $directory;

				if (!is_dir(DIR_IMAGE . $path)) {
					@mkdir(DIR_IMAGE . $path, 0777);
				}
			}

			if ($width_orig != $width || $height_orig != $height) {
				$image = new Image(DIR_IMAGE . $image_old);
				$image->resize($width, $height);
				$image->save(DIR_IMAGE . $image_new);
			} else {
				copy(DIR_IMAGE . $image_old, DIR_IMAGE . $image_new);
			}
		}
		
		$image_new = str_replace(' ', '%20', $image_new);  // fix bug when attach image on email (gmail.com). it is automatic changing space " " to +
		

		if (mime_content_type(DIR_IMAGE . $image_old) != 'image/svg+xml' && in_array($extension, array('svg', 'SVG'))) {
			$dom = new DOMDocument;
			$dom->loadXML(file_get_contents(DIR_IMAGE . $image_old));

			if ($dom) {
				$svg = simplexml_import_dom($dom);
			}
		} elseif (mime_content_type(DIR_IMAGE . $image_old) == 'image/svg+xml') {
			$svg = simplexml_load_file(DIR_IMAGE . $image_old);
		}

		if (isset($svg)) {
			if ($svg['width'] && $svg['height']) {
				$width_orig = (string)$svg['width'];
				$height_orig = (string)$svg['height'];

				if (is_numeric($width_orig) && is_numeric($height_orig)) {
					$width_orig = (string)$svg['width'];
					$height_orig = (string)$svg['height'];
				} elseif (substr($width_orig, -2) == 'px' && substr($height_orig, -2) == 'px') {
					$width_orig = str_replace('px', '', $width_orig);
					$height_orig = str_replace('px', '', $height_orig);

					if (!is_numeric($width_orig) && !is_numeric($height_orig)) {
						$width_orig = '';
						$height_orig = '';
					}
				}
			} elseif ($svg['viewBox']) {
				$viewbox = explode(' ', $svg['viewBox']);

				$height_orig = array_pop($viewbox);
				$width_orig = array_pop($viewbox);
			} else {
				$width_orig = '';
				$height_orig = '';
			}

			if (($width_orig && $height_orig) && ($width_orig != $width || $height_orig != $height)) {
				$scale_w = $width / $width_orig;
				$scale_h = $height / $height_orig;

				$scale = min($scale_w, $scale_h);

				$new_width = (int)($width_orig * $scale);
				$new_height = (int)($height_orig * $scale);

				$svg['width'] = $new_width;
				$svg['height'] = $new_height;

				$svg->asXML(DIR_IMAGE . $image_new);
			} else {
				$svg['width'] = $width;
				$svg['height'] = $height;

				$svg->asXML(DIR_IMAGE . $image_new);
			}
		}
      
		if ($this->request->server['HTTPS']) {
			return $this->config->get('config_ssl') . 'image/' . $image_new;
		} else {
			return $this->config->get('config_url') . 'image/' . $image_new;
		}
	}
}
