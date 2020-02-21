<?php
/**
 * Created by PhpStorm.
 * User: Dianka
 * Date: 22.07.2019
 * Time: 16:21
 */

return [
    'image_folder' =>[
        'avatars' => [
            'save_path' => 'public/img/avatars',
            'get_path' => 'api/storage/img/avatars/'
        ],
        'images' => [
            'save_path' => 'public/img/images',
            'get_path' => 'api/storage/img/images/'
        ],
    ],

    'video_folder' =>[
         'videos' => [
             'save_path' => 'public/video/videos',
             'get_path' => 'api/storage/video/videos/'
         ],
     ],
];