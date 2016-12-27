    function save() {
        $data['shopId'] = $this->input->post('shopId');
        $data['name'] = $name  = $this->input->post('name');
	    $this->load->library('form_validation');
	    $this->form_validation->set_rules('shopId', 'Shop Name', 'required');
        $this->form_validation->set_rules('name', 'Gallery Name', 'required');
	    
       	if ($this->form_validation->run() == FALSE) {
            $this->set_server_message(array(
                'type' => 'error',
                'message' => validation_errors()
            )); 
           	redirect('admin/gallery/create');
        } else {
	        $config['upload_path']          = './assets/uploads/gallery/';
	        $config['allowed_types']        = 'gif|jpg|png';
	        $config ['file_name']           = 'album_'.$name;
	        $config['max_size'] 			= 20000;
			$config['max_width'] 			= 102400;
			$config['max_height'] 			= 76800;

            $this->load->library('upload', $config);

                if ( ! $this->upload->do_upload('myfile')) {
                    $error = $this->upload->display_errors();
                    $this->set_server_message(array(
                        'type' => 'error',
                        'message' => $error
                    ));
	        		redirect('admin/gallery/create');
                } else {
                    //mkdir('./assets/uploads/gallery/'.$name);
                    $filedata = array('upload_data' => $this->upload->data());
                    $data['image'] = $filedata['upload_data']['file_name'];
	                $data['album'] = $gallery = $this->Album_model->save($data);
                    $this->set_server_message(array(
                        'type' => 'success',
                        'message' => 'Gallery Created successfully'
                    ));
                }
            }
            
            $albumId = $this->db->insert_id();
            mkdir('./assets/uploads/gallery/'.$albumId);

        $count = count($_FILES['userfile']['size']);
        if ($count > 0) {

            for ($i=0; $i < $count; $i++) { 
                //Get the temp file path
                $tmpFilePath = $_FILES['userfile']['tmp_name'][$i];
                $check = $_FILES["userfile"]["size"][$i] < 5242880;
                //Setup our new file name
                $newFileName = 'gallery'.$albumId.$_FILES['userfile']['name'][$i];
                //Setup our new file path
                $newFilePath = "./assets/uploads/gallery/".$albumId."/" . $newFileName;

                $imageFileType = pathinfo($newFilePath,PATHINFO_EXTENSION);

                    if($imageFileType == "jpg" || $imageFileType == "png" || $imageFileType == "jpeg") {

                        if($check) {
                        move_uploaded_file($tmpFilePath, $newFilePath);
                        $gal['album_id'] = $albumId;
                        $gal['image'] = $newFileName;
                        $img = $this->Gallery_model->save_gallery($gal);
                            if($img > 0) {
                                //$this->set_server_message(array('type' => 'success','message' => 'Image uploaded successfully'));
                            }
                        } else {
                            $this->set_server_message(array(
                                'type' => 'error',
                                'message' => 'File '. $_FILES['userfile']['name'][$i] .' exceeds the size 1 mb'
                            ));
                        }
                    } else {
                        $this->set_server_message(array(
                            'type' => 'error',
                            'message' => '.'.$imageFileType .' file format not supported'
                        ));
                    }
            }   
        }
            //redirect('admin/gallery/create');
            redirect('admin/gallery/edit/'.$albumId);
    }
