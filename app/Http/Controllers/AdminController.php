<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Requests;

use App\Moderator;

use App\User;

use App\UserPayment;

use App\Subscription;

use App\PayPerView;

use App\Admin;

use App\SubProfile;

use App\Notification;

use App\Category;

use App\RedeemRequest;

use App\SubCategory;

use App\SubCategoryImage;

use App\Genre;

use App\AdminVideo;

use App\AdminVideoImage;

use App\UserHistory;

use App\Wishlist;

use App\UserRating;

use App\Language;

use App\Settings;

use App\Page;

use App\Helpers\Helper;

use App\Helpers\EnvEditorHelper;

use App\Flag;

use Validator;

use Hash;

use Mail;

use DB;

use Auth;

use Exception;

use Redirect;

use Setting;

use Log;

use App\Jobs\CompressVideo;

use App\Jobs\NormalPushNotification;

class AdminController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('admin');  
    }

    public function login() {
        return view('admin.login')->withPage('admin-login')->with('sub_page','');
    }

    public function dashboard() {

        $id = Auth::guard('admin')->user()->id;

        $admin = Admin::find($id);

        $admin->token = Helper::generate_token();

        $admin->token_expiry = Helper::generate_token_expiry();

        $admin->save();
        
        $user_count = User::count();

        $provider_count = Moderator::count();

        $video_count = AdminVideo::count();
        
        $recent_videos = Helper::recently_added();

        $get_registers = get_register_count();

        $recent_users = get_recent_users();

        $total_revenue = total_revenue();

        $view = last_days(10);

        if (Setting::get('track_user_mail')) {

            user_track("StreamHash - New Visitor");

        }

        return view('admin.dashboard.dashboard')->withPage('dashboard')
                    ->with('sub_page','')
                    ->with('user_count' , $user_count)
                    ->with('video_count' , $video_count)
                    ->with('provider_count' , $provider_count)
                    ->with('get_registers' , $get_registers)
                    ->with('view' , $view)
                    ->with('total_revenue' , $total_revenue)
                    ->with('recent_users' , $recent_users)
                    ->with('recent_videos' , $recent_videos);
    }

    public function profile() {

        $id = Auth::guard('admin')->user()->id;

        $admin = Admin::find($id);

        return view('admin.account.profile')->with('admin' , $admin)->withPage('profile')->with('sub_page','');
    }

    public function profile_process(Request $request) {

        $validator = Validator::make( $request->all(),array(
                'name' => 'max:255',
                'email' => $request->id ? 'email|max:255|unique:admins,email,'.$request->id : 'email|max:255|unique:admins,email,NULL',
                'mobile' => 'digits_between:6,13',
                'address' => 'max:300',
                'id' => 'required|exists:admins,id',
                'picture' => 'mimes:jpeg,jpg,png'
            )
        );
        
        if($validator->fails()) {
            $error_messages = implode(',', $validator->messages()->all());
            return back()->with('flash_errors', $error_messages);
        } else {
            
            $admin = Admin::find($request->id);
            
            $admin->name = $request->has('name') ? $request->name : $admin->name;

            $admin->email = $request->has('email') ? $request->email : $admin->email;

            $admin->mobile = $request->has('mobile') ? $request->mobile : $admin->mobile;

            $admin->gender = $request->has('gender') ? $request->gender : $admin->gender;

            $admin->address = $request->has('address') ? $request->address : $admin->address;

            if($request->hasFile('picture')) {
                Helper::delete_picture($admin->picture, "/uploads/");
                $admin->picture = Helper::normal_upload_picture($request->picture);
            }
                
            $admin->remember_token = Helper::generate_token();
            $admin->is_activated = 1;
            $admin->save();

            return back()->with('flash_success', tr('admin_not_profile'));
            
        }
    
    }

    public function change_password(Request $request) {

        $old_password = $request->old_password;
        $new_password = $request->password;
        $confirm_password = $request->confirm_password;
        
        $validator = Validator::make($request->all(), [              
                'password' => 'required|min:6',
                'old_password' => 'required',
                'confirm_password' => 'required|min:6',
                'id' => 'required|exists:admins,id'
            ]);

        if($validator->fails()) {

            $error_messages = implode(',',$validator->messages()->all());

            return back()->with('flash_errors', $error_messages);

        } else {

            $admin = Admin::find($request->id);

            if(Hash::check($old_password,$admin->password))
            {
                $admin->password = Hash::make($new_password);
                $admin->save();

                return back()->with('flash_success', tr('password_change_success'));
                
            } else {
                return back()->with('flash_error', tr('password_mismatch'));
            }
        }

        $response = response()->json($response_array,$response_code);

        return $response;
    }

    public function users() {

        $users = User::orderBy('created_at','desc')->get();

        return view('admin.users.users')->withPage('users')
                        ->with('users' , $users)
                        ->with('sub_page','view-user');
    }

    public function add_user() {
        return view('admin.users.add-user')->with('page' , 'users')->with('sub_page','add-user');
    }

    public function edit_user(Request $request) {

        $user = User::find($request->id);
        return view('admin.users.edit-user')->withUser($user)->with('sub_page','view-user')->with('page' , 'users');
    }

    public function add_user_process(Request $request) {

        if($request->id != '') {

            $validator = Validator::make( $request->all(), array(
                        'name' => 'required|max:255',
                        'email' => 'required|email|max:255|unique:users,email,'.$request->id,
                        'mobile' => 'required|digits_between:6,13',
                    )
                );
        
        } else {
            $validator = Validator::make( $request->all(), array(
                    'name' => 'required|max:255',
                    'email' => 'required|email|max:255|unique:users,email',
                    'mobile' => 'required|digits_between:6,13',
                    'password' => 'required|min:6|confirmed',
                )
            );
        
        }
       
        if($validator->fails())
        {
            $error_messages = implode(',', $validator->messages()->all());
            return back()->with('flash_errors', $error_messages);
        } else {

            $new_user = 0;

            if($request->id != '') {

                $user = User::find($request->id);

                $message = tr('admin_not_user');

            } else {

                $new_user = 1;

                //Add New User

                $user = new User;
                
                $new_password = $request->password;
                $user->password = Hash::make($new_password);
                $message = tr('admin_add_user');
                $user->login_by = 'manual';
                $user->device_type = 'web';
            }

            $user->picture = asset('placeholder.png');

            $user->timezone = $request->has('timezone') ? $request->timezone : '';

            $user->name = $request->has('name') ? $request->name : '';
            $user->email = $request->has('email') ? $request->email: '';
            $user->mobile = $request->has('mobile') ? $request->mobile : '';
            
            $user->token = Helper::generate_token();
            $user->token_expiry = Helper::generate_token_expiry();
            $user->is_activated = 1;   

            $user->no_of_account = 1;

            $user->status = 1;   

            if($request->id == '') {
                
                $email_data['name'] = $user->name;
                $email_data['password'] = $new_password;
                $email_data['email'] = $user->email;

                $subject = tr('user_welcome_title');
                $page = "emails.admin_user_welcome";
                $email = $user->email;
                Helper::send_email($page,$subject,$email,$email_data);
            }

            $user->save();

            if ($new_user) {

                $sub_profile = new SubProfile;

                $sub_profile->user_id = $user->id;

                $sub_profile->name = $user->name;

                $sub_profile->picture = $user->picture;

                $sub_profile->status = DEFAULT_TRUE;

                $sub_profile->save();

            } else {

                $sub_profile = SubProfile::where('user_id', $request->id)->first();

                if (!$sub_profile) {

                    $sub_profile = new SubProfile;

                    $sub_profile->user_id = $user->id;

                    $sub_profile->name = $user->name;

                    $sub_profile->picture = $user->picture;

                    $sub_profile->status = DEFAULT_TRUE;

                    $sub_profile->save();

                }

            }

                $user->is_verified = 1;      

                $user->save();

            // Check the default subscription and save the user type 

            user_type_check($user->id);

            if($user) {

                register_mobile('web');

                if (Setting::get('track_user_mail')) {

                    user_track("StreamHash - New User Created");

                }

                return redirect('/admin/view/user/'.$user->id)->with('flash_success', $message);
            } else {
                return back()->with('flash_error', tr('admin_not_error'));
            }

        }
    
    }

    public function delete_user(Request $request) {
        
        if($user = User::where('id',$request->id)->first()) {

            // Check User Exists or not

            if ($user) {

                if ($user->device_type) {

                    // Load Mobile Registers

                    subtract_count($user->device_type);
                }

                if($user->picture)

                    Helper::delete_picture($user->picture, "/uploads/images/"); // Delete the old pic

                // After reduce the count from mobile register model delete the user

                if ($user->delete()) {

                    return back()->with('flash_success',tr('admin_not_user_del'));   
                }
            }
        }
        return back()->with('flash_error',tr('admin_not_error'));
    }



    public function user_approve(Request $request) {

        $user = User::find($request->id);

        $user->is_activated = $user->is_activated ? DEFAULT_FALSE : DEFAULT_TRUE;

        $user->save();

        if($user->is_activated ==1) {

            $message = tr('user_approve_success');
        } else {
            $message = tr('user_decline_success');
        }

        return back()->with('flash_success', $message);
    }

    /**
     * @uses Email verify for the user
     *
     * @param $user_id
     *
     * @return redirect back page with status of the email verification
     */

    public function user_verify_status($id) {

        if($data = User::find($id)) {

            $data->is_verified  = $data->is_verified ? 0 : 1;

            $data->save();

            return back()->with('flash_success' , $data->is_verified ? tr('user_verify_success') : tr('user_unverify_success'));

        } else {

            return back()->with('flash_error',tr('admin_not_error'));
            
        }
    }


    public function view_user($id) {

        if($user = User::find($id)) {

            return view('admin.users.user-details')
                        ->with('user' , $user)
                        ->withPage('users')
                        ->with('sub_page','users');

        } else {
            return back()->with('flash_error',tr('admin_not_error'));
        }
    }

    public function user_upgrade($id) {

        if($user = User::find($id)) {

            // Check the user is exists in moderators table

            if(!$moderator = Moderator::where('email' , $user->email)->first()) {

                $moderator_user = new Moderator;
                $moderator_user->name = $user->name;
                $moderator_user->email = $user->email;
                if($user->login_by == "manual") {
                    $moderator_user->password = $user->password;  
                    $new_password = "Please use you user login Pasword.";
                } else {
                    $new_password = time();
                    $new_password .= rand();
                    $new_password = sha1($new_password);
                    $new_password = substr($new_password, 0, 8);
                    $moderator_user->password = Hash::make($new_password);
                }

                $moderator_user->picture = $user->picture;
                $moderator_user->mobile = $user->mobile;
                $moderator_user->address = $user->address;
                $moderator_user->save();

                $email_data = array();

                $subject = tr('user_welcome_title');
                $page = "emails.moderator_welcome";
                $email = $user->email;
                $email_data['name'] = $moderator_user->name;
                $email_data['email'] = $moderator_user->email;
                $email_data['password'] = $new_password;

                Helper::send_email($page,$subject,$email,$email_data);

                $moderator = $moderator_user;

            }

            if($moderator) {
                $user->is_moderator = 1;
                $user->moderator_id = $moderator->id;
                $user->save();

                $moderator->is_activated = 1;
                $moderator->is_user = 1;
                $moderator->save();

                return back()->with('flash_warning',tr('admin_user_upgrade'));
            } else  {
                return back()->with('flash_error',tr('admin_not_error'));    
            }

        } else {
            return back()->with('flash_error',tr('admin_not_error'));
        }

    }

    public function user_upgrade_disable(Request $request) {

        if($moderator = Moderator::find($request->moderator_id)) {

            if($user = User::find($request->id)) {
                $user->is_moderator = 0;
                $user->save();
            }

            $moderator->is_activated = 0;

            $moderator->save();

            return back()->with('flash_success',tr('admin_user_upgrade_disable'));

        } else {

            return back()->with('flash_error',tr('admin_not_error'));
        }
    }

    public function view_history($id) {

       if($user = SubProfile::find($id)) {

        $user_history = UserHistory::where('user_id' , $id)
                        ->leftJoin('users' , 'user_histories.user_id' , '=' , 'users.id')
                        ->leftJoin('admin_videos' , 'user_histories.admin_video_id' , '=' , 'admin_videos.id')
                        ->select(
                            'users.name as username' , 
                            'users.id as user_id' , 
                            'user_histories.admin_video_id',
                            'user_histories.id as user_history_id',
                            'admin_videos.title',
                            'user_histories.created_at as date'
                            )
                        ->get();
                        
        return view('admin.users.user-history')
                    ->with('data' , $user_history)
                    ->with('user', $user)
                    ->withPage('users')
                        ->with('sub_page','users');

        } else {
            return back()->with('flash_error',tr('admin_not_error'));
        }
    }

    public function delete_history($id) {

        if($user_history = UserHistory::find($id)) {

            $user_history->delete();

            return back()->with('flash_success',tr('admin_not_history_del'));

        } else {
            return back()->with('flash_error',tr('admin_not_error'));
        }
    }

    public function view_wishlist($id) {

        if($user = SubProfile::find($id)) {

            $user_wishlist = Wishlist::where('user_id' , $id)
                            ->leftJoin('users' , 'wishlists.user_id' , '=' , 'users.id')
                            ->leftJoin('admin_videos' , 'wishlists.admin_video_id' , '=' , 'admin_videos.id')
                            ->select(
                                'users.name as username' , 
                                'users.id as user_id' , 
                                'wishlists.admin_video_id',
                                'wishlists.id as wishlist_id',
                                'admin_videos.title',
                                'wishlists.created_at as date'
                                )
                            ->get();

            return view('admin.users.user-wishlist')
                        ->with('data' , $user_wishlist)
                        ->with('user', $user)
                        ->withPage('users')
                        ->with('sub_page','users');

        } else {
            return back()->with('flash_error',tr('admin_not_error'));
        }
    }

    public function delete_wishlist($id) {

        if($user_wishlist = Wishlist::find($id)) {

            $user_wishlist->delete();

            return back()->with('flash_success',tr('admin_not_wishlist_del'));

        } else {
            return back()->with('flash_error',tr('admin_not_error'));
        }
    }

    public function moderators() {

        $moderators = Moderator::orderBy('created_at','desc')->get();

        return view('admin.moderators.moderators')->with('moderators' , $moderators)->withPage('moderators')->with('sub_page','view-moderator');
    }

    public function add_moderator() {
        return view('admin.moderators.add-moderator')->with('page' ,'moderators')->with('sub_page' ,'add-moderator');
    }

    public function edit_moderator($id) {

        $moderator = Moderator::find($id);

        return view('admin.moderators.edit-moderator')->with('moderator' , $moderator)->with('page' ,'moderators')->with('sub_page' ,'edit-moderator');
    }

    public function add_moderator_process(Request $request) {

        if($request->id != '') {
            $validator = Validator::make( $request->all(), array(
                        'name' => 'required|max:255',
                        'email' => 'required|email|max:255',
                        'mobile' => 'required|digits_between:6,13',
                    )
                );
        } else {
            $validator = Validator::make( $request->all(), array(
                    'name' => 'required|max:255',
                    'email' => 'required|email|max:255|unique:moderators,email',
                    'mobile' => 'required|digits_between:6,13',
                    'password' => 'required|min:6|confirmed',
                )
            );
        
        }
       
        if($validator->fails()) {
            $error_messages = implode(',', $validator->messages()->all());
            return back()->with('flash_errors', $error_messages);

        } else {

            if($request->id != '') {
                $user = Moderator::find($request->id);
                $message = tr('admin_not_moderator');
            } else {
                $message = tr('admin_add_moderator');
                //Add New User
                $user = new Moderator;
                /*$new_password = time();
                $new_password .= rand();
                $new_password = sha1($new_password);
                $new_password = substr($new_password, 0, 8);*/
                $new_password = $request->password;

                // print_r(Hash::make($new_password));


                $user->password = Hash::make($new_password);


                $user->is_activated = 1;

            }

            $user->picture = asset('placeholder.png');

            $user->timezone = $request->has('timezone') ? $request->timezone : '';
            $user->name = $request->has('name') ? $request->name : '';
            $user->email = $request->has('email') ? $request->email: '';
            $user->mobile = $request->has('mobile') ? $request->mobile : '';
            
            $user->token = Helper::generate_token();
            $user->token_expiry = Helper::generate_token_expiry();
                               

            if($request->id == ''){
                $email_data['name'] = $user->name;
                $email_data['password'] = $new_password;
                $email_data['email'] = $user->email;

                $subject = tr('user_welcome_title').Setting::get('site_name');
                $page = "emails.moderator_welcome";
                $email = $user->email;
                Helper::send_email($page,$subject,$email,$email_data);
            }

            $user->save();

            if($user) {

                if (Setting::get('track_user_mail')) {

                    user_track("StreamHash - Moderator Created");

                }

                return redirect('/admin/view/moderator/'.$user->id)->with('flash_success', $message);
            } else {
                return back()->with('flash_error', tr('admin_not_error'));
            }

        }
    
    }

    public function delete_moderator(Request $request) {

        if($moderator = Moderator::find($request->id)) {

            if($moderator->picture) {

                Helper::delete_picture($moderator->picture , '/uploads/images/');

            }

            $moderator->delete();

            return back()->with('flash_success',tr('admin_not_moderator_del'));

        } else {
            return back()->with('flash_error',tr('admin_not_error'));
        }
    }

    public function moderator_approve(Request $request) {

        $moderator = Moderator::find($request->id);

        $moderator->is_activated = 1;

        $moderator->save();

        if($moderator->is_activated ==1) {
            $message = tr('admin_not_moderator_approve');
        } else {
            $message = tr('admin_not_moderator_decline');
        }

        return back()->with('flash_success', $message);
    }

    public function moderator_decline(Request $request) {
        
        if($moderator = Moderator::find($request->id)) {
            
            $moderator->is_activated = 0;

            $moderator->save(); 

            $message = tr('admin_not_moderator_decline');
        
            return back()->with('flash_success', $message);  
        } else {
            return back()->with('flash_error' , tr('admin_not_error'));
        }

    
    
            
    }

    public function moderator_view_details($id) {

        if($moderator = Moderator::find($id)) {
            return view('admin.moderators.moderator-details')->with('moderator' , $moderator)
                        ->withPage('moderators')
                        ->with('sub_page','view-moderator');
        } else {
            return back()->with('flash_error',tr('admin_not_error'));
        }
    }

    public function categories() {

        $categories = Category::select('categories.id',
                            'categories.name' , 
                            'categories.picture',
                            'categories.is_series',
                            'categories.status',
                            'categories.is_approved',
                            'categories.created_by'
                        )
                        ->orderBy('categories.created_at', 'desc')
                        ->distinct('categories.id')
                        ->get();

        return view('admin.categories.categories')->with('categories' , $categories)->withPage('categories')->with('sub_page','view-categories');
    }

    public function add_category() {
        return view('admin.categories.add-category')->with('page' ,'categories')->with('sub_page' ,'add-category');
    }

    public function edit_category($id) {

        $category = Category::find($id);

        return view('admin.categories.edit-category')->with('category' , $category)->with('page' ,'categories')->with('sub_page' ,'edit-category');
    }

    public function add_category_process(Request $request) {

        if($request->id != '') {
            $validator = Validator::make( $request->all(), array(
                        'name' => 'required|max:255',
                        'picture' => 'mimes:jpeg,jpg,bmp,png',
                    )
                );
        } else {
            $validator = Validator::make( $request->all(), array(
                    'name' => 'required|max:255|unique:categories,name',
                    'picture' => 'required|mimes:jpeg,jpg,bmp,png',
                )
            );
        
        }
       
        if($validator->fails()) {
            $error_messages = implode(',', $validator->messages()->all());
            return back()->with('flash_errors', $error_messages);

        } else {

            if($request->id != '') {
                $category = Category::find($request->id);
                $message = tr('admin_not_category');
                if($request->hasFile('picture')) {
                    Helper::delete_picture($category->picture, "/uploads/images/");
                }
            } else {
                $message = tr('admin_add_category');
                //Add New User
                $category = new Category;
                $category->is_approved = DEFAULT_TRUE;
                $category->created_by = ADMIN;
            }

            $category->name = $request->has('name') ? $request->name : '';
            $category->is_series = $request->has('is_series') ? $request->is_series : 0;
            $category->status = 1;
            
            if($request->hasFile('picture') && $request->file('picture')->isValid()) {
                $category->picture = Helper::normal_upload_picture($request->file('picture'));
            }

            $category->save();

            if($category) {

                if (Setting::get('track_user_mail')) {

                    user_track("StreamHash - Category Created");

                }

                return back()->with('flash_success', $message);
            } else {
                return back()->with('flash_error', tr('admin_not_error'));
            }

        }
    
    }

    public function approve_category(Request $request) {

        $category = Category::find($request->id);

        $category->is_approved = $request->status;

        $category->save();

        // ($category->subCategory) ? $category->subCategory()->update(['is_approved' => $request->status]) : '';

        if ($request->status == 0) {
            foreach($category->subCategory as $sub_category)
            {                
                $sub_category->is_approved = $request->status;
                $sub_category->save();
            } 

            foreach($category->adminVideo as $video)
            {                
                $video->is_approved = $request->status;
                $video->save();
            } 

            foreach($category->genre as $genre)
            {                
                $genre->is_approved = $request->status;
                $genre->save();
            } 
        }

        $message = tr('admin_not_category_decline');

        if($category->is_approved == DEFAULT_TRUE){

            $message = tr('admin_not_category_approve');
        }

        return back()->with('flash_success', $message);
    
    }

    public function delete_category(Request $request) {
        
        $category = Category::where('id' , $request->category_id)->first();

        if($category) {  

            Helper::delete_picture($category->picture, "/uploads/images/");
            
            $category->delete();

            return back()->with('flash_success',tr('admin_not_category_del'));

        } else {

            return back()->with('flash_error',tr('admin_not_error'));

        }
    }


    public function sub_categories($category_id) {

        $category = Category::find($category_id);

        $sub_categories = SubCategory::where('category_id' , $category_id)
                        ->select(
                                'sub_categories.id as id',
                                'sub_categories.name as sub_category_name',
                                'sub_categories.description',
                                'sub_categories.is_approved',
                                'sub_categories.created_by'
                                )
                        ->orderBy('sub_categories.created_at', 'desc')
                        ->get();

        return view('admin.categories.subcategories.sub-categories')->with('category' , $category)->with('data' , $sub_categories)->withPage('categories')->with('sub_page','view-categories');
    }

    public function add_sub_category($category_id) {

        $category = Category::find($category_id);
    
        return view('admin.categories.subcategories.add-sub-category')->with('category' , $category)->with('page' ,'categories')->with('sub_page' ,'add-category');
    }

    public function edit_sub_category(Request $request) {

        $category = Category::find($request->category_id);

        $sub_category = SubCategory::find($request->sub_category_id);

        $sub_category_images = SubCategoryImage::where('sub_category_id' , $request->sub_category_id)
                                    ->orderBy('position' , 'ASC')->get();

        $genres = Genre::where('sub_category_id' , $request->sub_category_id)
                        ->orderBy('position' , 'asc')
                        ->get();

        return view('admin.categories.subcategories.edit-sub-category')
                ->with('category' , $category)
                ->with('sub_category' , $sub_category)
                ->with('sub_category_images' , $sub_category_images)
                ->with('genres' , $genres)
                ->with('page' ,'categories')
                ->with('sub_page' ,'');
    }

    public function add_sub_category_process(Request $request) {

        if($request->id != '') {
            $validator = Validator::make( $request->all(), array(
                        'category_id' => 'required|integer|exists:categories,id',
                        'id' => 'required|integer|exists:sub_categories,id',
                        'name' => 'required|max:255',
                        'picture1' => 'mimes:jpeg,jpg,bmp,png',
                       // 'picture2' => 'mimes:jpeg,jpg,bmp,png',
                       // 'picture3' => 'mimes:jpeg,jpg,bmp,png',
                    )
                );
        } else {
            $validator = Validator::make( $request->all(), array(
                    'name' => 'required|max:255',
                    'description' => 'required|max:255',
                    'picture1' => 'required|mimes:jpeg,jpg,bmp,png',
                    //'picture2' => 'required|mimes:jpeg,jpg,bmp,png',
                    //'picture3' => 'required|mimes:jpeg,jpg,bmp,png',
                )
            );
        
        }
       
        if($validator->fails()) {
            $error_messages = implode(',', $validator->messages()->all());
            return back()->with('flash_errors', $error_messages);

        } else {

            if($request->id != '') {

                $sub_category = SubCategory::find($request->id);

                $message = tr('admin_not_sub_category');

                if($request->hasFile('picture1')) {
                    Helper::delete_picture($sub_category->picture1, "/uploads/images/");
                }


            } else {
                $message = tr('admin_add_sub_category');
                //Add New User
                $sub_category = new SubCategory;

                $sub_category->is_approved = DEFAULT_TRUE;
                $sub_category->created_by = ADMIN;
            }

            $sub_category->category_id = $request->has('category_id') ? $request->category_id : '';
            
            if($request->has('name')) {
                $sub_category->name = $request->name;
            }

            if($request->has('description')) {
                $sub_category->description =  $request->description;   
            }

            $sub_category->save(); // Otherwise it will save empty values

           /* if($request->has('genre')) {

                foreach ($request->genre as $key => $genres) {
                    $genre = new Genre;
                    $genre->category_id = $request->category_id;
                    $genre->sub_category_id = $sub_category->id;
                    $genre->name = $genres;
                    $genre->status = DEFAULT_TRUE;
                    $genre->is_approved = DEFAULT_TRUE;
                    $genre->created_by = ADMIN;
                    $genre->position = $key+1;
                    $genre->save();
                }
            }*/
            
            if($request->hasFile('picture1')) {
                sub_category_image($request->file('picture1') , $sub_category->id,1);
            }

            if($request->hasFile('picture2')) {
                sub_category_image($request->file('picture2'), $sub_category->id , 2);
            }

            if($request->hasFile('picture3')) {
                sub_category_image($request->file('picture3'), $sub_category->id , 3);
            }

            if($sub_category) {

                if (Setting::get('track_user_mail')) {

                    user_track("StreamHash - Sub category Created");

                }

                return back()->with('flash_success', $message);
            } else {
                return back()->with('flash_error', tr('admin_not_error'));
            }

        }
    
    }

    public function approve_sub_category(Request $request) {

        $sub_category = SubCategory::find($request->id);

        $sub_category->is_approved = $request->status;

        $sub_category->save();

        if ($request->status == 0) {

            foreach($sub_category->adminVideo as $video)
            {                
                $video->is_approved = $request->status;
                $video->save();
            } 

            foreach($sub_category->genres as $genre)
            {                
                $genre->is_approved = $request->status;
                $genre->save();
            } 

        }

        $message = tr('admin_not_sub_category_decline');

        if($sub_category->is_approved == DEFAULT_TRUE){

            $message = tr('admin_not_sub_category_approve');
        }

        return back()->with('flash_success', $message);
    
    }

    public function delete_sub_category(Request $request) {

        $sub_category = SubCategory::where('id' , $request->id)->first();

        if($sub_category) {

            Helper::delete_picture($sub_category->picture1, "/uploads/images/");

            $sub_category->delete();

            return back()->with('flash_success',tr('admin_not_sub_category_del'));
        } else {
            return back()->with('flash_error',tr('admin_not_error'));
        }
    }

    public function add_genre($sub_category) {

        $subcategory = SubCategory::find($sub_category);

        $genre = new Genre;
    
        return view('admin.categories.subcategories.genres.create')->with('subcategory' , $subcategory)->with('page' ,'categories')->with('sub_page' ,'add-category')->with('genre', $genre);

    }

    public function save_genre(Request $request) {


        $validator = Validator::make( $request->all(), array(
                'category_id' => 'required|integer|exists:categories,id',
                'sub_category_id' => 'required|integer|exists:sub_categories,id',
                'name' => 'required|max:255',
                'video'=> ($request->id) ? 'mimes:mkv,mp4,qt' : 'required|mimes:mkv,mp4,qt',
                'image'=> ($request->id) ? 'mimes:jpeg,jpg,bmp,png' : 'required|mimes:jpeg,jpg,bmp,png',
            )
        );


        if($validator->fails()) {
            $error_messages = implode(',', $validator->messages()->all());
            return back()->with('flash_errors', $error_messages);

        } else {
            // To order the position of the genres
            $position = 1;

            if($check_position = Genre::where('sub_category_id' , $request->id)->orderBy('position' , 'desc')->first()) {
                $position = $check_position->position +1;
            } 

            $genre = $request->id ? Genre::find($request->id) : new Genre;
            $genre->category_id = $request->category_id;
            $genre->sub_category_id = $request->sub_category_id;
            $genre->name = $request->name;

            $genre->position = $position;
            $genre->status = DEFAULT_TRUE;
            $genre->is_approved = DEFAULT_TRUE;
            $genre->created_by = ADMIN;


            if($request->hasFile('video')) {

                if ($genre->id) {

                    if ($genre->video) {

                        Helper::delete_picture($genre->video, '/uploads/videos/original/');  

                    }  
                }

                $video = Helper::video_upload($request->file('video'), 1);


                $genre->video = $video['db_url'];  
            }

            if($request->hasFile('image')) {

                if ($genre->id) {

                    if ($genre->image) {

                        Helper::delete_picture($genre->image,'/uploads/images/');  

                    }  
                }

                $genre->image =  Helper::normal_upload_picture($request->file('image'), 1);
            }


            if($request->hasFile('subtitle')) {

                if ($genre->id) {

                    if ($genre->subtitle) {

                        Helper::delete_picture($genre->subtitle, "/uploads/subtitles/");  

                    }  
                }

                $genre->subtitle =  Helper::subtitle_upload($request->file('subtitle'));

            }

            $genre->save();

            $message = ($request->id) ? tr('admin_edit_genre') : tr('admin_add_genre');

            if($genre) {

                if(!$request->id) {

                    $genre->unique_id = $genre->id;

                    $genre->save();
                }

                if (Setting::get('track_user_mail')) {

                    user_track("StreamHash - Genre Created");

                }
                return back()->with('flash_success', $message);
            } else {
                return back()->with('flash_error', tr('admin_not_error'));
            }
        }
    
    }

    public function edit_genre($sub_category_id, $genre_id) {

        $subcategory = SubCategory::find($sub_category_id);

        $genre = Genre::find($genre_id);
    
        return view('admin.categories.subcategories.genres.edit')->with('subcategory' , $subcategory)->with('page' ,'categories')->with('sub_page' ,'add-category')->with('genre', $genre);
    }

    public function genres($sub_category) {

        $subcategory = SubCategory::find($sub_category);

        $genres = Genre::where('sub_category_id' , $sub_category)
                        ->leftjoin('sub_categories', 'sub_categories.id', '=', 'genres.sub_category_id')
                        ->leftjoin('categories', 'categories.id', '=', 'genres.category_id')
                        ->select(
                                'genres.id as genre_id',
                                'categories.name as category_name',
                                'sub_categories.name as sub_category_name',
                                'genres.name as genre_name',
                                'genres.video',
                                'genres.subtitle',
                                'genres.image',
                                'genres.is_approved',
                                'genres.created_at',
                                'sub_categories.id as sub_category_id',
                                'sub_categories.category_id as category_id'
                                )
                        ->orderBy('genres.created_at', 'desc')
                        ->get();

        return view('admin.categories.subcategories.genres.index')->with('sub_category' , $subcategory)->with('data' , $genres)->withPage('categories')->with('sub_page','view-categories');
    }

    public function approve_genre(Request $request) {

        $genre = Genre::find($request->id);

        $genre->is_approved = $request->status;

        $genre->save();

        if ($request->status == 0) {
            foreach($genre->adminVideo as $video)
            {                
                $video->is_approved = $request->status;
                $video->save();
            }
        }

        $message = tr('admin_not_genre_decline');

        if($genre->is_approved == DEFAULT_TRUE){

            $message = tr('admin_not_genre_approve');
        }

        return back()->with('flash_success', $message);
    
    }

    public function view_genre($id) {

        $genre = Genre::where('genres.id' , $id)
                    ->leftJoin('categories' , 'genres.category_id' , '=' , 'categories.id')
                    ->leftJoin('sub_categories' , 'genres.sub_category_id' , '=' , 'sub_categories.id')
                    ->select('genres.id as genre_id' ,'genres.name as genre_name' , 
                             'genres.position' , 'genres.status' , 
                             'genres.is_approved' , 'genres.created_at as genre_date' ,
                             'genres.created_by',
                                'genres.video',
                            'genres.image',
                             'genres.category_id as category_id',
                             'genres.sub_category_id',
                             'categories.name as category_name',
                             'genres.unique_id',
                             'genres.subtitle',
                             'sub_categories.name as sub_category_name')
                    ->orderBy('genres.position' , 'asc')
                    ->first();

        return view('admin.categories.subcategories.genres.view-genre')->with('genre' , $genre)
                    ->withPage('videos')
                    ->with('sub_page','view-videos');
        
    }

    public function delete_genre(Request $request) {
        if($genre = Genre::where('id',$request->id)->first()) {

            Helper::delete_picture($genre->image,'/uploads/images/'); 

            if ($genre->video) {

                Helper::delete_picture($genre->video, '/uploads/videos/original/');   

            }

            if ($genre->subtitle) {

                Helper::delete_picture($genre->subtitle, "/uploads/subtitles/");

            }  

            $genre->delete();

            return back()->with('flash_success',tr('admin_not_genre_del'));

        } else {
            return back()->with('flash_error',tr('admin_not_error'));
        }
    }

    public function videos(Request $request) {

        $videos = AdminVideo::leftJoin('categories' , 'admin_videos.category_id' , '=' , 'categories.id')
                    ->leftJoin('sub_categories' , 'admin_videos.sub_category_id' , '=' , 'sub_categories.id')
                    ->leftJoin('genres' , 'admin_videos.genre_id' , '=' , 'genres.id')
                    ->select('admin_videos.id as video_id' ,'admin_videos.title' , 
                             'admin_videos.description' , 'admin_videos.ratings' , 
                             'admin_videos.reviews' , 'admin_videos.created_at as video_date' ,
                             'admin_videos.default_image',
                             'admin_videos.banner_image',
                             'admin_videos.amount',
                             'admin_videos.admin_amount',
                             'admin_videos.user_amount',
                             'admin_videos.unique_id',
                             'admin_videos.type_of_user',
                             'admin_videos.type_of_subscription',
                             'admin_videos.category_id as category_id',
                             'admin_videos.sub_category_id',
                             'admin_videos.genre_id',
                             'admin_videos.is_home_slider',
                             'admin_videos.watch_count',
                             'admin_videos.compress_status',
                             'admin_videos.trailer_compress_status',
                             'admin_videos.status','admin_videos.uploaded_by',
                             'admin_videos.edited_by','admin_videos.is_approved',
                             'admin_videos.video_subtitle',
                             'admin_videos.trailer_subtitle',
                             'categories.name as category_name' , 'sub_categories.name as sub_category_name' ,
                             'genres.name as genre_name')
                    ->orderBy('admin_videos.created_at' , 'desc')
                    ->get();

        return view('admin.videos.videos')->with('videos' , $videos)
                    ->withPage('videos')
                    ->with('sub_page','view-videos');
   
    }

    public function add_video(Request $request) {

        $categories = Category::where('categories.is_approved' , 1)
                        ->select('categories.id as id' , 'categories.name' , 'categories.picture' ,
                            'categories.is_series' ,'categories.status' , 'categories.is_approved')
                        ->leftJoin('sub_categories' , 'categories.id' , '=' , 'sub_categories.category_id')
                        ->groupBy('sub_categories.category_id')
                        ->havingRaw("COUNT(sub_categories.id) > 0")
                        ->orderBy('categories.name' , 'asc')
                        ->get();

         return view('admin.videos.video_upload')
                ->with('categories' , $categories)
                ->with('page' ,'videos')
                ->with('sub_page' ,'add-video');

    }

    public function edit_video(Request $request) {

        Log::info("Queue Driver ".envfile('QUEUE_DRIVER'));

        $categories =  $categories = Category::where('categories.is_approved' , 1)
                        ->select('categories.id as id' , 'categories.name' , 'categories.picture' ,
                            'categories.is_series' ,'categories.status' , 'categories.is_approved')
                        ->leftJoin('sub_categories' , 'categories.id' , '=' , 'sub_categories.category_id')
                        ->groupBy('sub_categories.category_id')
                        ->havingRaw("COUNT(sub_categories.id) > 0")
                        ->orderBy('categories.name' , 'asc')
                        ->get();

        $video = AdminVideo::where('admin_videos.id' , $request->id)
                    ->leftJoin('categories' , 'admin_videos.category_id' , '=' , 'categories.id')
                    ->leftJoin('sub_categories' , 'admin_videos.sub_category_id' , '=' , 'sub_categories.id')
                    ->leftJoin('genres' , 'admin_videos.genre_id' , '=' , 'genres.id')
                    ->select('admin_videos.id as video_id' ,'admin_videos.title' , 
                             'admin_videos.description' , 'admin_videos.ratings' , 
                             'admin_videos.reviews' , 'admin_videos.created_at as video_date' ,'admin_videos.is_banner','admin_videos.banner_image',
                             'admin_videos.video','admin_videos.trailer_video',
                             'admin_videos.video_type','admin_videos.video_upload_type',
                             'admin_videos.publish_time','admin_videos.duration',

                             'admin_videos.category_id as category_id',
                             'admin_videos.sub_category_id',
                             'admin_videos.genre_id',
                             'admin_videos.details',
                             'admin_videos.default_image',
                             'categories.name as category_name' , 'categories.is_series',
                             'sub_categories.name as sub_category_name' ,
                             'genres.name as genre_name')
                    ->orderBy('admin_videos.created_at' , 'desc')
                    ->first();
        if(!$video) {

            return back()->with('flash_error', tr('something_error'));

        }

        $page = 'videos';
        $sub_page = 'add-video';

        $subcategories = [];

        if($video->category_id) {
            $subcategories = get_sub_categories($video->category_id);
        }

        if($video->is_banner == 1) {
            $page = 'banner-videos';
            $sub_page = 'banner-videos';
        }

         return view('admin.videos.edit-video')
                ->with('categories' , $categories)
                ->with('video' ,$video)
                ->with('page' ,$page)
                ->with('sub_page' ,$sub_page)->with('subCategories',$subcategories);
    }

    public function add_video_process(Request $request) {

        if($request->has('video_type') && $request->video_type == VIDEO_TYPE_UPLOAD) {

            $video_validator = Validator::make( $request->all(), array(
                        'video'     => 'required|mimes:mkv,mp4,qt',
                        'trailer_video'  => ($request->genre_id) ? 'mimes:mkv,mp4,qt' : 'required|mimes:mkv,mp4,qt',
                        )
                    );

            $video_link = $request->file('video');

            $trailer_video = $request->hasFile('trailer_video') ? $request->file('trailer_video') : '';


        } else {

            $video_validator = Validator::make( $request->all(), array(
                        'other_video'     => 'required',
                        'other_trailer_video'  => 'required',
                        )
                    );

            $video_link = $request->other_video;

            $trailer_video = $request->other_trailer_video;

        }

        if($video_validator) {


             if($video_validator->fails()) {

                $error_messages = implode(',', $video_validator->messages()->all());


                if ($request->has('ajax_key')) {

                    return ['error_messages'=>$error_messages, 'error_code'=>500];

                } else {

                    return back()->with('flash_errors', $error_messages);

                }
            }
        }
        $validator = Validator::make( $request->all(), array(
                    'title'         => 'required|max:255',
                    'description'   => 'required',
                    'category_id'   => 'required|integer|exists:categories,id',
                    'sub_category_id' => 'required|integer|exists:sub_categories,id,category_id,'.$request->category_id,
                    'genre'     => 'exists:genres,id,sub_category_id,'.$request->sub_category_id,
                    'default_image' => 'required|mimes:jpeg,jpg,bmp,png',
                    'banner_image' => 'mimes:jpeg,jpg,bmp,png',
                    'other_image1' => 'required|mimes:jpeg,jpg,bmp,png',
                    'other_image2' => 'required|mimes:jpeg,jpg,bmp,png',
                    'ratings' => 'required',
                    'reviews' => 'required',
                    'duration'=>'required',
                    )
                );
        if($validator->fails()) {

            $error_messages = implode(',', $validator->messages()->all());

            if ($request->has('ajax_key')) {

                return ['error_messages'=>$error_messages, 'error_code'=>500];

            } else  {

                return back()->with('flash_errors', $error_messages);

            }

        } else {

            $video = new AdminVideo;
            $video->title = $request->title;
            $video->description = $request->description;
            $video->category_id = $request->category_id;
            $video->sub_category_id = $request->sub_category_id;
            $video->genre_id = $request->has('genre_id') ? $request->genre_id : 0;

            if($request->has('duration')) {
                $video->duration = $request->duration;
            }

            $main_video_duration = null;

            $trailer_video_duration = null;

            if($request->video_type == VIDEO_TYPE_UPLOAD) {

                $video->video_upload_type = $request->video_upload_type;

                if($request->video_upload_type == VIDEO_UPLOAD_TYPE_s3) {

                    $video->video = Helper::upload_picture($video_link);

                    if ($trailer_video) {

                        $video->trailer_video = Helper::upload_picture($trailer_video);

                    }

                } else {
                    $main_video_duration = Helper::video_upload($video_link, $request->compress_video);
                    $video->video = $main_video_duration['db_url'];

                    $video->video_resolutions = ($request->video_resolutions) ? implode(',', $request->video_resolutions) : '';


                    if ($trailer_video) {

                        $trailer_video_duration = Helper::video_upload($trailer_video, $request->compress_video);
                        $video->trailer_video = $trailer_video_duration['db_url'];  
                        
                        
                        $video->trailer_video_resolutions = ($request->video_resolutions) ? implode(',', $request->video_resolutions) : '';

                    }
  
                }     

            } elseif($request->video_type == VIDEO_TYPE_YOUTUBE) {

                $video->video = get_youtube_embed_link($video_link);
                $video->trailer_video = get_youtube_embed_link($trailer_video);
            } else {
                $video->video = $video_link;
                $video->trailer_video = $trailer_video;
            }

            $video->video_type = $request->video_type;


            $video->publish_time = date('Y-m-d H:i:s', strtotime($request->publish_time));
            
            $video->default_image = Helper::normal_upload_picture($request->file('default_image'));

            if($request->is_banner) {
                $video->is_banner = 1;
                $video->banner_image = Helper::normal_upload_picture($request->file('banner_image'));
            }

            $video->details = $request->has('details') ? $request->details : '';

            $video->ratings = $request->ratings;
            $video->reviews = $request->reviews;             

            if(strtotime($request->publish_time) < strtotime(date('Y-m-d H:i:s'))) {

                $video->status = DEFAULT_TRUE;

            } else {

                $video->status = DEFAULT_FALSE;

            }


            if($request->hasFile('video_subtitle')) {

                $video->video_subtitle =  Helper::subtitle_upload($request->file('video_subtitle'));


            }

            if($request->hasFile('trailer_subtitle')) {

                $video->trailer_subtitle =  Helper::subtitle_upload($request->file('trailer_subtitle'));

            }

            if (empty($video->video_resolutions)) {
                $video->compress_status = DEFAULT_TRUE;
                $video->trailer_compress_status = DEFAULT_TRUE;
                $video->is_approved = DEFAULT_TRUE;
            }
            
            $video->uploaded_by = ADMIN;

            // dd($video);
            Log::info("Approved : ".$video->is_approved);

            $video->save();

            Log::info("saved Video Object : ".'Success');


            if($video) {

                $video->unique_id = $video->id;

                $video->save();

                if($video->is_approved) {

                    Log::info("Send Notification ".$request->send_notification);

                    if ($request->send_notification) {

                        Notification::save_notification($video->id);

                    }

                }

                if($video->video_resolutions) {
                    if ($main_video_duration) {
                        $inputFile = $main_video_duration['baseUrl'];
                        $local_url = $main_video_duration['local_url'];
                        $file_name = $main_video_duration['file_name'];
                        if (file_exists($inputFile)) {
                            Log::info("Main queue Videos : ".'Success');
                            dispatch(new CompressVideo($inputFile, $local_url, MAIN_VIDEO, $video->id, $file_name, $request->send_notification));
                            Log::info("Main Compress Status : ".$video->compress_status);
                            Log::info("Main queue completed : ".'Success');
                        }
                    }
                    if ($trailer_video_duration) {
                        if ($trailer_video) {
                            $inputFile = $trailer_video_duration['baseUrl'];
                            $local_url = $trailer_video_duration['local_url'];
                            $file_name = $trailer_video_duration['file_name'];
                            if (file_exists($inputFile)) {
                                Log::info("Trailer queue Videos : ".'Success');
                                dispatch(new CompressVideo($inputFile, $local_url, TRAILER_VIDEO, $video->id,$file_name,$request->send_notification));
                                Log::info("Trailer Compress Status : ".$video->trailer_compress_status);
                                Log::info("Trailer queue completed : ".'Success');
                            }
                        }
                    }
                }
                
                Helper::upload_video_image($request->file('other_image1'),$video->id,2);

                Helper::upload_video_image($request->file('other_image2'),$video->id,3);

                if (envfile('QUEUE_DRIVER') != 'redis') {

                    \Log::info("Queue Driver : ".envfile('QUEUE_DRIVER'));

                    $video->compress_status = DEFAULT_TRUE;

                    $video->trailer_compress_status = DEFAULT_TRUE;

                    $video->save();
                }

                if (Setting::get('track_user_mail')) {

                    user_track("StreamHash - Video Created");

                }

                /*if($video->is_banner)
                    return redirect(route('admin.banner.videos'));
                else*/
                if ($request->has('ajax_key')) {
                    Log::info('Video Id Ajax : '.$video->id);
                    return ['id'=>route('admin.view.video', array('id'=>$video->id))];
                } else  {
                    Log::info('Video Id : '.$video->id);
                    return redirect(route('admin.view.video', array('id'=>$video->id)));
                }
            } else {

                if($request->has('ajax_key')) {
                    
                    return tr('admin_not_error');

                } else { 
                    
                    return back()->with('flash_error', tr('admin_not_error'));

                }
            }
        }
    
    }

    public function edit_video_process(Request $request) {

        Log::info("Initiaization Edit Process : ".print_r($request->all(),true));


        $video = AdminVideo::find($request->id);

        $video_validator = array();

        $video_link = $video->video;

        $trailer_video = $video->trailer_video;

        // dd($request->all());

        if($request->has('video_type') && $request->video_type == VIDEO_TYPE_UPLOAD) {

            Log::info("Video Type : ".$request->has('video_type'));

            if (isset($request->video)) {
                if ($request->video != '') {

                    $video_validator = Validator::make( $request->all(), array(
                            'video'     => 'required|mimes:mkv,mp4,qt',
                            // 'trailer_video'  => 'required|mimes:mkv,mp4,qt',
                            )
                        );

                    $video_link = $request->hasFile('video') ? $request->file('video') : array();   

                }
            }

            if (isset($request->trailer_video)) {
                if ($request->trailer_video != '') {
                    $video_validator = Validator::make( $request->all(), array(
                            // 'video'     => 'required|mimes:mkv,mp4,qt',
                             'trailer_video'  => ($request->genre_id) ? 'mimes:mkv,mp4,qt' : 'required|mimes:mkv,mp4,qt',
                            )
                        );

                    $trailer_video = $request->hasFile('trailer_video') ? $request->file('trailer_video') : array();
                }
            }
        

        } elseif($request->has('video_type') && in_array($request->video_type , array(VIDEO_TYPE_YOUTUBE,VIDEO_TYPE_OTHER))) {

            $video_validator = Validator::make( $request->all(), array(
                        'other_video'     => 'required',
                        'other_trailer_video'  => 'required',
                        )
                    );

            $video_link = $request->has('other_video') ? $request->other_video : array();

            $trailer_video = $request->has('other_trailer_video') ? $request->other_trailer_video : array();
        }

        if($video_validator) {

             if($video_validator->fails()) {
                $error_messages = implode(',', $video_validator->messages()->all());
                if ($request->has('ajax_key')) {
                    return $error_messages;
                } else {
                    return back()->with('flash_errors', $error_messages);
                }
            }
        }

        $validator = Validator::make( $request->all(), array(
                    'id' => 'required|integer|exists:admin_videos,id',
                    'title'         => 'max:255',
                    'description'   => '',
                    'category_id'   => 'required|integer|exists:categories,id',
                    'sub_category_id' => 'required|integer|exists:sub_categories,id,category_id,'.$request->category_id,
                    'genre'     => 'exists:genres,id,sub_category_id,'.$request->sub_category_id,
                    // 'video'     => 'mimes:mkv,mp4,qt',
                    // 'trailer_video'  => 'mimes:mkv,mp4,qt',
                    'default_image' => 'mimes:jpeg,jpg,bmp,png',
                    'other_image1' => 'mimes:jpeg,jpg,bmp,png',
                    'other_image2' => 'mimes:jpeg,jpg,bmp,png',
                    'ratings' => 'required',
                    'reviews' => 'required',
                    )
                );

        if($validator->fails()) {
            $error_messages = implode(',', $validator->messages()->all());
            if ($request->has('ajax_key')) {
                return $error_messages;
            } else {
                return back()->with('flash_errors', $error_messages);
            }

        } else {

            Log::info("Success validation checking : Success");

            $video->title = $request->has('title') ? $request->title : $video->title;

            $video->description = $request->has('description') ? $request->description : $video->description;

            $video->category_id = $request->has('category_id') ? $request->category_id : $video->category_id;

            $video->sub_category_id = $request->has('sub_category_id') ? $request->sub_category_id : $video->sub_category_id;

            $video->genre_id = $request->has('genre_id') ? $request->genre_id : $video->genre_id;

            if($request->has('duration')) {
                $video->duration = $request->duration;
            }

            if(strtotime($request->publish_time) < strtotime(date('Y-m-d H:i:s'))) {
                $video->status = DEFAULT_TRUE;
            } else {
                $video->status = DEFAULT_FALSE;
            }

            $video->details = $request->has('details') ? $request->details : $video->details;

            $main_video_url = null;
            $trailer_video_url = null;

            if($request->video_type == VIDEO_TYPE_UPLOAD && $video_link) {

                 Log::info("To Be upload videos : ".'Success');

                // Check Previous Video Upload Type, to delete the videos

                if($video->video_upload_type == VIDEO_UPLOAD_TYPE_s3) {
                    Helper::s3_delete_picture($video->video);   

                    if ($trailer_video) {

                        Helper::s3_delete_picture($video->trailer_video);  

                    }
                } else {
                    $videopath = '/uploads/videos/original/';

                    // dd($request->all());

                    if ($request->hasFile('video')) {
                        Helper::delete_picture($video->video, $videopath); 
                        // @TODO
                        $splitVideos = ($video->video_resolutions) 
                                    ? explode(',', $video->video_resolutions)
                                    : [];
                        foreach ($splitVideos as $key => $value) {
                           Helper::delete_picture($video->video, $videopath.$value.'/');
                        }
                        Log::info("Deleted Main Video : ".'Success');   
                    }
                    if ($request->hasFile('trailer_video')) {

                        if ($trailer_video) {
                            Helper::delete_picture($video->trailer_video, $videopath);
                            // @TODO
                            $splitTrailer = ($video->trailer_video_resolutions) 
                                        ? explode(',', $video->trailer_video_resolutions)
                                        : [];
                            foreach ($splitTrailer as $key => $value) {
                               Helper::delete_picture($video->trailer_video, $videopath.$value.'/');
                            }
                            Log::info("Deleted Trailer Video : ".'Success');

                        }
                    }
                }

                if($request->video_upload_type == VIDEO_UPLOAD_TYPE_s3) {
                    $video->video = Helper::upload_picture($video_link);

                    if ($trailer_video) {

                        $video->trailer_video = Helper::upload_picture($trailer_video);

                    } 

                } else {
                    if ($request->hasFile('video')) {
                        $video->compress_status = DEFAULT_FALSE;
                        $video->is_approved = DEFAULT_FALSE;
                        $main_video_url = Helper::video_upload($video_link, $request->compress_video);
                        Log::info("New Video Uploaded ( Main Video ) : ".'Success');
                        $video->video = $main_video_url['db_url'];
                        $video->video_resolutions = ($request->video_resolutions) ? implode(',', $request->video_resolutions) : null;
                    } else {
                        $video->video = $video_link;
                    }
                    // dd($request->hasFile('trailer_video'));
                    if ($request->hasFile('trailer_video')) {
                        $video->trailer_compress_status = DEFAULT_FALSE;
                        $video->is_approved = DEFAULT_FALSE;
                        $trailer_video_url = Helper::video_upload($trailer_video, $request->compress_video);
                        Log::info("New Video Uploaded ( Trailer Video ) : ".'Success');
                        $video->trailer_video = $trailer_video_url['db_url']; 
                        $video->trailer_video_resolutions = ($request->video_resolutions) ? implode(',', $request->video_resolutions) : null; 
                    } else {
                        $video->trailer_video = $trailer_video;
                    }
                
                    Log::info("Video Resoltuions : ".print_r($video->video_resolutions, true));
                    Log::info("Trailer Video Resoltuions : ".print_r($video->trailer_video_resolutions, true));
                }                

            } elseif($request->video_type == VIDEO_TYPE_YOUTUBE && $video_link && $trailer_video) {

                $video->video = get_youtube_embed_link($video_link);
                $video->trailer_video = get_youtube_embed_link($trailer_video);
            } else {
                $video->video = $video_link ? $video_link : $video->video;
                $video->trailer_video = $trailer_video ? $trailer_video : $video->trailer_video;
            }

            if($request->hasFile('default_image')) {
                Helper::delete_picture($video->default_image, "/uploads/images/");
                $video->default_image = Helper::normal_upload_picture($request->file('default_image'));
            }

            if($video->is_banner == 1) {
                if($request->hasFile('banner_image')) {
                    Helper::delete_picture($video->banner_image, "/uploads/images/");
                    $video->banner_image = Helper::normal_upload_picture($request->file('banner_image'));
                }
            }

            $video->video_type = $request->video_type ? $request->video_type : $video->video_type;

            $video->video_upload_type = $request->video_upload_type ? $request->video_upload_type : $video->video_upload_type;

            $video->ratings = $request->has('ratings') ? $request->ratings : $video->ratings;

            $video->reviews = $request->has('reviews') ? $request->reviews : $video->reviews;

            $video->edited_by = ADMIN;

            $video->unique_id = $video->id;

            if($video->video_type != VIDEO_TYPE_UPLOAD) {
                $video->trailer_resize_path = null;
                $video->video_resize_path = null;
                $video->trailer_video_resolutions = null;
                $video->video_resolutions = null;
            }

            if (empty($video->video_resolutions)) {
                $video->compress_status = DEFAULT_TRUE;
                $video->trailer_compress_status = DEFAULT_TRUE;
                $video->is_approved = DEFAULT_TRUE;
                Log::info("Empty Resoltuions");
            }

            Log::info("Approved : ".$video->is_approved);


            if($request->hasFile('trailer_subtitle')) {

                if ($video->id) {

                    if ($video->trailer_subtitle) {

                        Helper::delete_picture($video->trailer_subtitle, "/uploads/subtitles/");  

                    }  
                }

                $video->trailer_subtitle =  Helper::subtitle_upload($request->file('trailer_subtitle'));

            }

            if($request->hasFile('video_subtitle')) {

                if ($video->id) {

                    if ($video->video_subtitle) {

                        Helper::delete_picture($video->video_subtitle, "/uploads/subtitles/");  

                    }  
                }

                $video->video_subtitle =  Helper::subtitle_upload($request->file('video_subtitle'));

            }

            $video->save();

            Log::info("saved Video Object : ".'Success');

            if($video) {
                if ($request->hasFile('video') && $video->video_resolutions) {
                    if ($main_video_url) {
                        $inputFile = $main_video_url['baseUrl'];
                        $local_url = $main_video_url['local_url'];
                        $file_name = $main_video_url['file_name'];
                        if (file_exists($inputFile)) {
                            Log::info("Main queue Videos : ".'Success');
                            dispatch(new CompressVideo($inputFile, $local_url, MAIN_VIDEO, $video->id,$file_name,$request->send_notification));
                            Log::info("Main Compress Status : ".$video->compress_status);
                            Log::info("Main queue completed : ".'Success');
                        }
                    }
                }

                if($request->hasFile('trailer_video') && $video->trailer_video_resolutions) {
                    if ($trailer_video_url) {
                        $inputFile = $trailer_video_url['baseUrl'];
                        $local_url = $trailer_video_url['local_url'];
                        $file_name = $trailer_video_url['file_name'];
                        if (file_exists($inputFile)) {
                            Log::info("Trailer queue Videos : ".'Success');
                            dispatch(new CompressVideo($inputFile, $local_url, TRAILER_VIDEO, $video->id, $file_name,$request->send_notification));
                            Log::info("Trailer Compress Status : ".$video->compress_status);
                            Log::info("Trailer queue completed : ".'Success');
                        }
                    }
                }

                if($request->hasFile('other_image1')) {
                    Helper::upload_video_image($request->file('other_image1'),$video->id,2);  
                }

                if($request->hasFile('other_image2')) {
                   Helper::upload_video_image($request->file('other_image2'),$video->id,3); 
                }

                if($video->is_approved) {

                    Log::info("Send Notification ".$request->send_notification);

                    if ($request->send_notification) {

                        Notification::save_notification($video->id);

                    }

                }


                if (envfile('QUEUE_DRIVER') != 'redis') {

                    \Log::info("Queue Driver : ".envfile('QUEUE_DRIVER'));

                    $video->compress_status = DEFAULT_TRUE;

                    $video->trailer_compress_status = DEFAULT_TRUE;

                    $video->save();
                }

                if (Setting::get('track_user_mail')) {

                    user_track("StreamHash - Updated Video");

                }

                if ($request->has('ajax_key')) {
                    return ['id'=>route('admin.view.video', array('id'=>$video->id))];
                } else {
                    return redirect(route('admin.view.video', array('id'=>$video->id)));
                }

            } else {
                if ($request->has('ajax_key')) {
                    return tr('admin_not_error');
                } else {
                    return back()->with('flash_error', tr('admin_not_error'));
                }
            }
        }
    
    }

    public function view_video(Request $request) {

        $validator = Validator::make($request->all() , [
                'id' => 'required|exists:admin_videos,id'
            ]);

        if($validator->fails()) {
            $error_messages = implode(',', $validator->messages()->all());
            return back()->with('flash_errors', $error_messages);
        } else {
            $videos = AdminVideo::where('admin_videos.id' , $request->id)
                    ->leftJoin('categories' , 'admin_videos.category_id' , '=' , 'categories.id')
                    ->leftJoin('sub_categories' , 'admin_videos.sub_category_id' , '=' , 'sub_categories.id')
                    ->leftJoin('genres' , 'admin_videos.genre_id' , '=' , 'genres.id')
                    ->select('admin_videos.id as video_id' ,'admin_videos.title' , 
                             'admin_videos.description' , 'admin_videos.ratings' , 
                             'admin_videos.reviews' , 'admin_videos.created_at as video_date' ,
                             'admin_videos.video','admin_videos.trailer_video',
                             'admin_videos.default_image','admin_videos.banner_image','admin_videos.is_banner','admin_videos.video_type',
                             'admin_videos.video_upload_type',
                             'admin_videos.amount',
                             'admin_videos.type_of_user',
                             'admin_videos.type_of_subscription',
                             'admin_videos.category_id as category_id',
                             'admin_videos.sub_category_id',
                             'admin_videos.genre_id',
                             'admin_videos.video_type',
                             'admin_videos.uploaded_by',
                             'admin_videos.ppv_created_by',
                             'admin_videos.details',
                             'admin_videos.watch_count',
                             'admin_videos.admin_amount',
                             'admin_videos.user_amount',
                             'admin_videos.video_upload_type',
                             'admin_videos.duration',
                             'admin_videos.redeem_amount',
                             'admin_videos.compress_status',
                             'admin_videos.trailer_compress_status',
                             'admin_videos.video_resolutions',
                             'admin_videos.video_resize_path',
                             'admin_videos.trailer_resize_path',
                             'admin_videos.is_approved',
                             'admin_videos.unique_id',
                             'admin_videos.video_subtitle',
                             'admin_videos.trailer_subtitle',
                             'admin_videos.trailer_video_resolutions',
                             'categories.name as category_name' , 'sub_categories.name as sub_category_name' ,
                             'genres.name as genre_name')
                    ->orderBy('admin_videos.created_at' , 'desc')
                    ->first();

        $videoPath = $video_pixels = $trailer_video_path = $trailer_pixels = $trailerstreamUrl = $videoStreamUrl = '';

        $ios_trailer_video = $videos->trailer_video;

        $ios_video = $videos->video;

        if ($videos->video_type == VIDEO_TYPE_UPLOAD && $videos->video_upload_type == VIDEO_UPLOAD_TYPE_DIRECT) {

            if(check_valid_url($videos->trailer_video)) {

                if(Setting::get('streaming_url'))
                    $trailerstreamUrl = Setting::get('streaming_url').get_video_end($videos->trailer_video);

                if(Setting::get('HLS_STREAMING_URL'))
                    $ios_trailer_video = Setting::get('HLS_STREAMING_URL').get_video_end($videos->trailer_video);
            }

            if(check_valid_url($videos->video)) {

                if(Setting::get('streaming_url'))
                    $videoStreamUrl = Setting::get('streaming_url').get_video_end($videos->video);

                if(Setting::get('HLS_STREAMING_URL'))
                    $ios_video = Setting::get('HLS_STREAMING_URL').get_video_end($videos->video);
            }
            

            if (\Setting::get('streaming_url')) {
                if ($videos->is_approved == 1) {
                    if($videos->trailer_video_resolutions) {
                        $trailerstreamUrl = Helper::web_url().'/uploads/smil/'.get_video_end_smil($videos->trailer_video).'.smil';
                    } 
                    if ($videos->video_resolutions) {
                        $videoStreamUrl = Helper::web_url().'/uploads/smil/'.get_video_end_smil($videos->video).'.smil';
                    }
                }
            } else {

                $videoPath = $videos->video_resize_path ? $videos->video.','.$videos->video_resize_path : $videos->video;
                $video_pixels = $videos->video_resolutions ? 'original,'.$videos->video_resolutions : 'original';
                $trailer_video_path = $videos->trailer_video_path ? $videos->trailer_video.','.$videos->trailer_video_path : $videos->trailer_video;
                $trailer_pixels = $videos->trailer_video_resolutions ? 'original'.$videos->trailer_video_resolutions : 'original';
            }

            $trailerstreamUrl = $trailerstreamUrl ? $trailerstreamUrl : $videos->trailer_video;
            $videoStreamUrl = $videoStreamUrl ? $videoStreamUrl : $videos->video;
        } else {

            $trailerstreamUrl = $videos->trailer_video;
            
            $videoStreamUrl = $videos->video;

             if($videos->video_type == VIDEO_TYPE_YOUTUBE) {

                $videoStreamUrl = $ios_video = get_youtube_embed_link($videos->video);

                $trailerstreamUrl =  $ios_trailer_video = get_youtube_embed_link($videos->trailer_video);
                

            }
        }
        
        $admin_video_images = AdminVideoImage::where('admin_video_id' , $request->id)
                                ->orderBy('is_default' , 'desc')
                                ->get();

        $page = 'videos';
        $sub_page = 'add-video';

        if($videos->is_banner == 1) {
            $page = 'banner-videos';
            $sub_page = 'banner-videos';
        }

        return view('admin.videos.view-video')->with('video' , $videos)
                    ->with('video_images' , $admin_video_images)
                    ->withPage($page)
                    ->with('sub_page',$sub_page)
                    ->with('videoPath', $videoPath)
                    ->with('video_pixels', $video_pixels)
                    ->with('ios_trailer_video', $ios_trailer_video)
                    ->with('ios_video', $ios_video)
                    ->with('trailer_video_path', $trailer_video_path)
                    ->with('trailer_pixels', $trailer_pixels)
                    ->with('videoStreamUrl', $videoStreamUrl)
                    ->with('trailerstreamUrl', $trailerstreamUrl);
        }
    }

    public function approve_video($id) {

        $video = AdminVideo::find($id);

        $video->is_approved = DEFAULT_TRUE;

        if (empty($video->publish_time) || $video->publish_time == '0000-00-00 00:00:00') {

            $video->publish_time = date('Y-m-d H:i:s');

        }

        $video->save();

        if($video->is_approved == DEFAULT_TRUE)
        {
            Notification::save_notification($video->id);
            
            $message = tr('admin_not_video_approve');
        }
        else
        {
            $message = tr('admin_not_video_decline');
        }
        return back()->with('flash_success', $message);
    }


    /**
     * Function Name : publish_video()
     * To Publish the video for user
     *
     * @param int $id : Video id
     *
     * @return Flash Message
     */
    public function publish_video($id) {
        // Load video based on Auto increment id
        $video = AdminVideo::find($id);
        // Check the video present or not
        if ($video) {
            $video->status = DEFAULT_TRUE;
            $video->publish_time = date('Y-m-d H:i:s');
            // Save the values in DB
            if ($video->save()) {
                return back()->with('flash_success', tr('admin_published_video_success'));
            }
        }
        return back()->with('flash_error', tr('admin_published_video_failure'));
    }


    public function decline_video($id) {
        
        $video = AdminVideo::find($id);

        $video->is_approved = DEFAULT_FALSE;

        $video->save();

        if($video->is_approved == DEFAULT_TRUE){
            $message = tr('admin_not_video_approve');
        } else {
            $message = tr('admin_not_video_decline');
        }

        return back()->with('flash_success', $message);
    }

    public function delete_video($id) {

        if($video = AdminVideo::where('id' , $id)->first())  {


            Helper::delete_picture($video->video, "/uploads/videos/original/");

            Helper::delete_picture($video->subtitle, "/uploads/subtitles/"); 

            if ($video->banner_image) {

                Helper::delete_picture($video->banner_image, "/uploads/images/");
            }

            Helper::delete_picture($video->default_image, "/uploads/images/");

            if ($video->video_resize_path) {

                $explode = explode(',', $video->video_resize_path);

                if (count($explode) > 0) {


                    foreach ($explode as $key => $exp) {


                        Helper::delete_picture($exp, "/uploads/videos/original/");

                    }

                }    

            }


            if($video->trailer_resize_path) {

                $explode = explode(',', $video->trailer_resize_path);

                if (count($explode) > 0) {


                    foreach ($explode as $key => $exp) {


                        Helper::delete_picture($exp, "/uploads/videos/original/");

                    }

                }    

            }

            $video->delete();
        }

        return back()->with('flash_success', 'Video deleted successfully');
    }

    public function slider_video($id) {

        $video = AdminVideo::where('is_home_slider' , 1 )->update(['is_home_slider' => 0]); 

        $video = AdminVideo::where('id' , $id)->update(['is_home_slider' => 1] );

        return back()->with('flash_success', tr('slider_success'));
    
    }

    public function banner_videos(Request $request) {

        $videos = AdminVideo::leftJoin('categories' , 'admin_videos.category_id' , '=' , 'categories.id')
                    ->leftJoin('sub_categories' , 'admin_videos.sub_category_id' , '=' , 'sub_categories.id')
                    ->leftJoin('genres' , 'admin_videos.genre_id' , '=' , 'genres.id')
                    ->where('admin_videos.is_banner' , 1 )
                    ->select('admin_videos.id as video_id' ,'admin_videos.title' , 
                             'admin_videos.description' , 'admin_videos.ratings' , 
                             'admin_videos.reviews' , 'admin_videos.created_at as video_date' ,
                             'admin_videos.default_image',
                             'admin_videos.banner_image',

                             'admin_videos.category_id as category_id',
                             'admin_videos.sub_category_id',
                             'admin_videos.genre_id',
                             'admin_videos.is_home_slider',

                             'admin_videos.status','admin_videos.uploaded_by',
                             'admin_videos.edited_by','admin_videos.is_approved',

                             'categories.name as category_name' , 'sub_categories.name as sub_category_name' ,
                             'genres.name as genre_name')
                    ->orderBy('admin_videos.created_at' , 'desc')
                    ->get();

        return view('admin.banner_videos.banner-videos')->with('videos' , $videos)
                    ->withPage('banner-videos')
                    ->with('sub_page','view-banner-videos');
   
    }

    public function add_banner_video(Request $request) {

        $categories = Category::where('categories.is_approved' , 1)
                        ->select('categories.id as id' , 'categories.name' , 'categories.picture' ,
                            'categories.is_series' ,'categories.status' , 'categories.is_approved')
                        ->leftJoin('sub_categories' , 'categories.id' , '=' , 'sub_categories.category_id')
                        ->groupBy('sub_categories.category_id')
                        ->havingRaw("COUNT(sub_categories.id) > 0")
                        ->orderBy('categories.name' , 'asc')
                        ->get();

        return view('admin.banner_videos.banner-video-upload')
                ->with('categories' , $categories)
                ->with('page' ,'banner-videos')
                ->with('sub_page' ,'add-banner-video');

    }

    public function change_banner_video($id) {

        $video = AdminVideo::find($id);

        $video->is_banner = 0 ;

        $video->save();

        $message = tr('change_banner_video_success');
       
        return back()->with('flash_success', $message);
    }

    public function user_ratings() {
            
            $user_reviews = UserRating::leftJoin('users', 'user_ratings.user_id', '=', 'users.id')
                ->select('user_ratings.id as rating_id', 'user_ratings.rating', 
                         'user_ratings.comment', 
                         'users.first_name as user_first_name', 
                         'users.last_name as user_last_name', 
                         'users.id as user_id', 'user_ratings.created_at')
                ->orderBy('user_ratings.id', 'ASC')
                ->get();
            return view('admin.reviews')->with('name', 'User')->with('reviews', $user_reviews);
    }

    public function delete_user_ratings(Request $request) {

        if($user = UserRating::find($request->id)) {
            $user->delete();
        }

        return back()->with('flash_success', tr('admin_not_ur_del'));
    }

    public function user_payments() {
        $payments = UserPayment::orderBy('created_at' , 'desc')->get();

        return view('admin.payments.user-payments')->with('data' , $payments)->with('page','payments')->with('sub_page','user-payments'); 
    }

    public function email_settings() {

        $admin_id = \Auth::guard('admin')->user()->id;

        $result = EnvEditorHelper::getEnvValues();

        \Auth::guard('admin')->loginUsingId($admin_id);

        return view('admin.email-settings')->with('result',$result)->withPage('email-settings')->with('sub_page',''); 
    }


    public function email_settings_process(Request $request) {

        $email_settings = ['MAIL_DRIVER' , 'MAIL_HOST' , 'MAIL_PORT' , 'MAIL_USERNAME' , 'MAIL_PASSWORD' , 'MAIL_ENCRYPTION'];

        $admin_id = \Auth::guard('admin')->user()->id;

        foreach ($email_settings as $key => $data) {

            // if($request->has($data)) {

                \Enveditor::set($data,$request->$data);
            //}

           
        }
        
        $result = EnvEditorHelper::getEnvValues();

        return redirect(route('clear-cache'))->with('result' , $result)->with('flash_success' , tr('email_settings_success'));

    }

    public function settings() {

        $settings = array();

        $result = EnvEditorHelper::getEnvValues();

        $languages = Language::where('status', DEFAULT_TRUE)->get();

        return view('admin.settings.settings')->with('settings' , $settings)->with('result', $result)->withPage('settings')->with('sub_page','')->with('languages' , $languages); 
    }

    public function payment_settings() {

        $settings = array();

        return view('admin.payment-settings')->with('settings' , $settings)->withPage('payment-settings')->with('sub_page',''); 
    }

    public function theme_settings() {

        $settings = array();

        $settings[] =  Setting::get('theme');

        if(Setting::get('theme')!= 'default') {
            $settings[] = 'default';
        }

        if(Setting::get('theme')!= 'teen') {
            $settings[] = 'teen';
        }

        return view('admin.theme.theme-settings')->with('settings' , $settings)->withPage('theme-settings')->with('sub_page',''); 
    }

    public function settings_process(Request $request) {

        $settings = Settings::all();

        $check_streaming_url = "";

        $refresh = "";

        if($settings) {

            foreach ($settings as $setting) {

                $key = $setting->key;
               
                if($setting->key == 'site_icon') {

                    if($request->hasFile('site_icon')) {
                        
                        if($setting->value) {
                            Helper::delete_picture($setting->value, "/uploads/");
                        }

                        $setting->value = Helper::normal_upload_picture($request->file('site_icon'));
                    
                    }
                    
                } else if($setting->key == 'site_logo') {

                    if($request->hasFile('site_logo')) {

                        if($setting->value) {

                            Helper::delete_picture($setting->value, "/uploads/");
                        }

                        $setting->value = Helper::normal_upload_picture($request->file('site_logo'));
                    }

                } else if($setting->key == 'home_page_bg_image') {

                    if($request->hasFile('home_page_bg_image')) {

                        if($setting->value) {

                            Helper::delete_picture($setting->value, "/uploads/");
                        }

                        $setting->value = Helper::normal_upload_picture($request->file('home_page_bg_image'));
                    }

                } else if($setting->key == 'common_bg_image') {

                    if($request->hasFile('common_bg_image')) {

                        if($setting->value) {

                            Helper::delete_picture($setting->value, "/uploads/");
                        }

                        $setting->value = Helper::normal_upload_picture($request->file('common_bg_image'));
                    }

                } else if($setting->key == 'streaming_url') {

                    if($request->has('streaming_url') && $request->streaming_url != $setting->value) {

                        if(check_nginx_configure()) {
                            $setting->value = $request->streaming_url;
                        } else {
                            $check_streaming_url = " !! ====> Please Configure the Nginx Streaming Server.";
                        }
                    }  

                } else if($setting->key == "theme") {

                    if($request->has('theme')) {
                        change_theme($setting->value , $request->$key);
                        $setting->value = $request->theme;
                    }

                } else if($setting->key == 'default_lang') {

                    if ($request->default_lang != $setting->value) {

                        $refresh = $request->default_lang;

                    }

                    $setting->value = $request->$key;

                } else if($setting->key == "admin_commission") {

                    $setting->value = $request->admin_commission < 100 ? $request->admin_commission : 100;

                    $user_commission = $request->admin_commission < 100 ? 100 - $request->admin_commission : 0;

                    $user_commission_details = Settings::where('key' , 'user_commission')->first();

                    if(count($user_commission_details) > 0) {

                        $user_commission_details->value = $user_commission;


                        $user_commission_details->save();
                    }


                } else if($setting->key == 'site_name') {

                    if($request->has('site_name')) {

                        $site_name  = preg_replace("/[^A-Za-z0-9]/", "", $request->site_name);

                        \Enveditor::set("SITENAME", $site_name);

                        $setting->value = $request->site_name;

                    }

                } else {

                    if (isset($_REQUEST[$key])) {

                        $setting->value = $request->$key;

                    }

                }

                $setting->save();
            
            }

        }

        // if($request->has('app_url')) {

        //      \Enveditor::set("ANGULAR_SITE_URL",$request->app_url);

        // }

        if ($refresh) {
            $fp = fopen(base_path() .'/config/new_config.php' , 'w');
            fwrite($fp, "<?php return array( 'locale' => '".$refresh."', 'fallback_locale' => '".$refresh."');?>");
            fclose($fp);
            \Log::info("Key : ".config('app.locale'));
            
        }
        
        
        $message = "Settings Updated Successfully"." ".$check_streaming_url;
        
        return redirect(route('clear-cache'))->with('setting', $settings)->with('flash_success', $message);    
    
    }

    public function help() {
        return view('admin.static.help')->withPage('help')->with('sub_page' , "");
    }

    public function static_pages_index() {

        $pages = Page::orderBy('updated_at' , 'desc')->get();

        return view('admin.pages.index')->with('page','static-pages')->with('sub_page',"static-pages-list")->with('data',$pages);
    }

    public function static_pages_add() {

        $all_pages = Page::all();

        $static_keys = ['about' , 'contact' , 'privacy' , 'terms' , 'help', 'others'];

        $pages = [];

        foreach ($all_pages as $key => $page) {

            if ($page->type != 'others') {

                $pages[] = $page->type;

            }

        }

        return view('admin.pages.create')->with('page','static-pages')->with('sub_page',"static-pages-create")
                ->with('view_pages',$pages)->with('static_keys', $static_keys);
    }

    public function static_pages_edit($id) {

        $page = Page::find($id);

        if($page) {
            return view('admin.pages.edit')->withPage('static-pages')->with('sub_page',"static-pages-list")->with('data',$page);
        } else {
            return back()->with('flash_error',tr('admin_not_error'));
        }
    }

    public function static_pages_save(Request $request) {

        $type = $request->type;
        $id = $request->id;
        $heading = $request->heading;
        $description = $request->description;

        $validator = Validator::make($request->all(),
            array('heading' => 'required',
                'description' => 'required'));

        if($validator->fails()) {
            $error = $validator->messages()->all();
            return back()->with('flash_errors',$error);
        } else {

            if($request->has('id')) {

                $pages = Page::find($id);
                $pages->heading = $heading;
                $pages->description = $description;
                $pages->save();

            } else {

                $check_page = "";

                if ($type != 'others') {

                    $check_page = Page::where('type',$type)->first();

                }
                
                if(!$check_page) {
                    $pages = new Page;
                    $pages->type = $type;
                    $pages->heading = $heading;
                    $pages->description = $description;
                    $pages->save();
                } else {
                    return back()->with('flash_error',tr('page_already_alert'));
                }
            }
            if($pages) {
                return back()->with('flash_success',tr('page_create_success'));
            } else {
                return back()->with('flash_error',tr('admin_not_error'));
            }
        }
    }

    public function static_pages_delete($id) {

        $page = Page::where('id',$id)->delete();

        if($page) {
            return back()->with('flash_success',tr('page_delete_success'));
        } else {
            return back()->with('flash_error',tr('admin_not_error'));
        }
    }

    public function custom_push() {

        return view('admin.static.push')->with('title' , "Custom Push")->with('page' , "custom-push");

    }

    public function custom_push_process(Request $request) {

        $validator = Validator::make(
            $request->all(),
            array( 'message' => 'required')
        );

        if($validator->fails()) {

            $error = $validator->messages()->all();

            return back()->with('flash_errors',$error);

        } else {

            $message = $request->message;
            $title = Setting::get('site_name');
            $message = $message;
            
            \Log::info($message);

            $id = 'all';

            Helper::send_notification($id,$title,$message);

            return back()->with('flash_success' , tr('push_send_success'));
        }
    }

    /**
     * Function Name : spam_videos()
     * Load all the videos from flag table
     *
     * @return all the spam videos
     */
    public function spam_videos() {
        // Load all the videos from flag table
        $model = Flag::groupBy('video_id')->get();
        // Return array of values
        return view('admin.spam_videos.spam_videos')->with('model' , $model)
                        ->with('page' , 'videos')
                        ->with('subPage' , 'spam_videos');
    }

    /**
     * Function Name : view_users()
     * Load all the flags based on the video id
     *
     * @param integer $id Video id
     *
     * @return all the spam videos
     */
    public function view_users($id) {
        // Load all the users
        $model = Flag::where('video_id', $id)->get();
        // Return array of values
        return view('admin.spam_videos.user_report')->with('model' , $model)
                        ->with('page' , 'Spam Videos')
                        ->with('subPage' , 'User Reports');   
    }

    /**
     * Function Name : video_payments()
     * To get payments based on the video subscription
     *
     * @return array of payments
     */
    public function video_payments() {

        $payments = PayPerView::orderBy('created_at' , 'desc')->get();

        return view('admin.payments.video-payments')->with('data' , $payments)->withPage('payments')->with('sub_page','video-subscription'); 
    }

    /**
     * Function Name : save_video_payment
     * Brief : To save the payment details
     *
     * @param integer $id Video Id
     * @param object  $request Object (Post Attributes)
     *
     * @return flash message
     */
    public function save_video_payment($id, Request $request){

        // Load Video Model

        $model = AdminVideo::find($id);

        // dd($request->all(),$model);

        // Get post attribute values and save the values
        if ($model) {
            if ($data = $request->all()) {
                // Update the post
                if (AdminVideo::where('id', $id)->update($data)) {
                    // Redirect into particular value
                    return back()->with('flash_success', tr('payment_added'));       
                } 
            }
        }
        return back()->with('flash_error', tr('admin_published_video_failure'));
    }

    /**
     * Function Name : save_common_settings
     * Save the values in env file
     *
     * @param object $request Post Attribute values
     * 
     * @return settings values
     */
    
    public function save_common_settings(Request $request) {

        $admin_id = \Auth::guard('admin')->user()->id;

        foreach ($request->all() as $key => $data) {

           // if($request->has($key)) {
                \Enveditor::set($key,$data);
            // }
        }

        $settings = Settings::all();

        $check_streaming_url = "";

        $refresh = "";

        if($settings) {

            foreach ($settings as $setting) {

                $key = $setting->key;
               
                if($setting->key == 'site_icon') {

                    if($request->hasFile('site_icon')) {
                        
                        if($setting->value) {
                            Helper::delete_picture($setting->value, "/uploads/");
                        }

                        $setting->value = Helper::normal_upload_picture($request->file('site_icon'));
                    
                    }
                    
                } else if($setting->key == 'site_logo') {

                    if($request->hasFile('site_logo')) {

                        if($setting->value) {

                            Helper::delete_picture($setting->value, "/uploads/");
                        }

                        $setting->value = Helper::normal_upload_picture($request->file('site_logo'));
                    }

                } else if($setting->key == 'home_page_bg_image') {

                    if($request->hasFile('home_page_bg_image')) {

                        if($setting->value) {

                            Helper::delete_picture($setting->value, "/uploads/");
                        }

                        $setting->value = Helper::normal_upload_picture($request->file('home_page_bg_image'));
                    }

                } else if($setting->key == 'common_bg_image') {

                    if($request->hasFile('common_bg_image')) {

                        if($setting->value) {

                            Helper::delete_picture($setting->value, "/uploads/");
                        }

                        $setting->value = Helper::normal_upload_picture($request->file('common_bg_image'));
                    }

                } else if($setting->key == "theme") {

                    if($request->has('theme')) {
                        change_theme($setting->value , $request->$key);
                        $setting->value = $request->theme;
                    }

                } else if($setting->key == 'default_lang') {

                        if ($request->default_lang != $setting->value) {

                            $refresh = $request->default_lang;

                        }

                    $setting->value = $request->$key;

                } else if($setting->key == "admin_commission") {

                    $setting->value = $request->admin_commission < 100 ? $request->admin_commission : 100;

                    $user_commission = $request->admin_commission < 100 ? 100 - $request->admin_commission : 0;

                    $user_commission_details = Settings::where('key' , 'user_commission')->first();

                    if(count($user_commission_details) > 0) {

                        $user_commission_details->value = $user_commission;


                        $user_commission_details->save();
                    }


                } else {

                    if (isset($_REQUEST[$key])) {

                        $setting->value = $request->$key;

                    }

                }

                $setting->save();
            
            }

        }

        return redirect(route('clear-cache'))->with('setting', $settings);
    }

    /**
     * Function Name : remove_payper_view()
     * To remove pay per view
     * 
     * @return falsh success
     */
    public function remove_payper_view($id) {
        
        // Load video model using auto increment id of the table
        $model = AdminVideo::find($id);
        if ($model) {
            $model->amount = 0;
            $model->type_of_subscription = 0;
            $model->type_of_user = 0;
            $model->save();
            if ($model) {
                return back()->with('flash_success' , tr('removed_pay_per_view'));
            }
        }
        return back()->with('flash_error' , tr('admin_published_video_failure'));
    }

     public function subscriptions() {

        $data = Subscription::orderBy('created_at','desc')->whereNotIn('status', [DELETE_STATUS])->get();

        return view('admin.subscriptions.index')->withPage('subscriptions')
                        ->with('data' , $data)
                        ->with('sub_page','view-subscription');        

    }

    public function user_subscriptions($id) {

        $data = Subscription::orderBy('created_at','desc')->whereNotIn('status', [DELETE_STATUS])->get();

         $payments = []; 

        if($id) {

            $payments = UserPayment::orderBy('created_at' , 'desc')
                        ->where('user_id' , $id)->get();

        }


        return view('admin.subscriptions.user_plans')->withPage('users')
                        ->with('subscriptions' , $data)
                        ->with('id', $id)
                        ->with('sub_page','users')->with('payments', $payments);        

    }

    public function user_subscription_save($s_id, $u_id) {

        // Load 

        $load = UserPayment::where('user_id', $u_id)->orderBy('created_at', 'desc')->first();

        $payment = new UserPayment();

        $payment->subscription_id = $s_id;

        $payment->user_id = $u_id;

        $payment->amount = ($payment->subscription) ? $payment->subscription->amount  : 0;

        $payment->payment_id = ($payment->amount > 0) ? uniqid(str_replace(' ', '-', 'PAY')) : 'Free Plan'; 

        if ($load) {
            $payment->expiry_date = date('Y-m-d H:i:s', strtotime("+{$payment->subscription->plan} months", strtotime($load->expiry_date)));
        } else {
            $payment->expiry_date = date('Y-m-d H:i:s',strtotime("+{$payment->subscription->plan} months"));
        }

        $payment->status = DEFAULT_TRUE;

        if ($payment->save())  {

            $payment->user->user_type = DEFAULT_TRUE;

            if ($payment->user->save()) {

                return back()->with('flash_success', tr('subscription_applied_success'));

            }

        }

         return back()->with('flash_errors', tr('went_wrong'));

    }

    public function subscription_create() {

        return view('admin.subscriptions.create')->with('page' , 'subscriptions')
                    ->with('sub_page','subscriptions-add');
    }

    public function subscription_edit($unique_id) {

        $data = Subscription::where('unique_id' ,$unique_id)->first();

        return view('admin.subscriptions.edit')->withData($data)
                    ->with('sub_page','subscriptions-view')
                    ->with('page' , 'subscriptions ');

    }

    public function subscription_save(Request $request) {

        $validator = Validator::make($request->all(),[
                'title' => 'required|max:255',
                'plan' => 'required',
                'amount' => 'required',
                'no_of_account'=>'required',
        ]);
        
        if($validator->fails()) {

            $error_messages = implode(',', $validator->messages()->all());

            return back()->with('flash_errors', $error_messages);

        } else {

            if($request->popular_status) {
                Subscription::where('popular_status' , 1)->update(['popular_status' => 0]);
            }


            if($request->id != '') {

                $model = Subscription::find($request->id);

                $model->update($request->all());

            } else {
                $model = Subscription::create($request->all());
                $model->status = 1;
                $model->popular_status = $request->popular_status ? 1 : 0;
                $model->unique_id = $model->title;
                $model->no_of_account = $request->no_of_account;
                $model->save();
            }
        
            if($model) {
                return redirect(route('admin.subscriptions.view', $model->unique_id))->with('flash_success', $request->id ? tr('subscription_update_success') : tr('subscription_create_success'));

            } else {
                return back()->with('flash_error',tr('admin_not_error'));
            }
        }
    
        
    }

    /** 
     * 
     * Subscription View
     *
     */

    public function subscription_view($unique_id) {

        if($data = Subscription::where('unique_id' , $unique_id)->first()) {

            return view('admin.subscriptions.view')
                        ->with('data' , $data)
                        ->withPage('subscriptions')
                        ->with('sub_page','subscriptions-view');

        } else {
            return back()->with('flash_error',tr('admin_not_error'));
        }
   
    }


    public function subscription_delete(Request $request) {

        if($data = Subscription::where('id',$request->id)->first()) {

            $data->status = DELETE_STATUS;

            $data->save();

            return back()->with('flash_success',tr('subscription_delete_success'));

        } else {
            return back()->with('flash_error',tr('admin_not_error'));
        }
        
    }

    /** 
     * Subscription status change
     * 
     *
     */

    public function subscription_status($unique_id) {

        if($data = Subscription::where('unique_id' , $unique_id)->first()) {

                $data->status  = $data->status ? 0 : 1;

                $data->save();

                return back()->with('flash_success' , $data->status ? tr('subscription_approve_success') : tr('subscription_decline_success'));
        } else {
            return back()->with('flash_error',tr('admin_not_error'));
        }
    }

    /** 
     * Subscription Popular status change
     * 
     *
     */

    public function subscription_popular_status($unique_id) {

        if($data = Subscription::where('unique_id' , $unique_id)->first()) {

            Subscription::where('popular_status' , 1)->update(['popular_status' => 0]);

            $data->popular_status  = $data->popular_status ? 0 : 1;

            $data->save();

            return back()->with('flash_success' , tr('subscription_popular_success'));
                
        } else {
            return back()->with('flash_error',tr('admin_not_error'));
        }
    }

    /** 
     * View list of users based on the selected Subscription
     *
     */

    public function subscription_users($id) {

        $user_ids = [];

        $users = UserPayment::where('subscription_id' , $id)->select('user_id')->get();

        foreach ($users as $key => $value) {
            $user_ids[] = $value->user_id;
        }

        $subscription = Subscription::find($id);

        $data = User::whereIn('id' , $user_ids)->orderBy('created_at','desc')->get();


        return view('admin.users.users')->withPage('users')
                        ->with('users' , $data)
                        ->with('sub_page','view-user')
                        ->with('subscription' , $subscription);
    }

    public function subProfiles($id) {

        $users = SubProfile::where('user_id', $id)->orderBy('created_at','desc')->get();

        return view('admin.users.sub_profiles')->withPage('users')
                        ->with('users' , $users)
                        ->with('sub_page','view-user');


    }


    public function user_redeem_requests($id = "") {

        $base_query = RedeemRequest::orderBy('status' , 'asc');

        $user = [];

        if($id) {
            $base_query = $base_query->where('user_id' , $id);

            $user = User::find($id);
        }

        $data = $base_query->get();

        return view('admin.moderators.redeems')->withPage('redeems')->with('sub_page' , 'redeems')->with('data' , $data)->with('user' , $user);
    
    }

    public function user_redeem_pay(Request $request) {

        $validator = Validator::make($request->all() , [
            'redeem_request_id' => 'required|exists:redeem_requests,id',
            'paid_amount' => 'required', 
            ]);

        if($validator->fails()) {

            return back()->with('flash_error' , $validator->messages()->all())->withInput();

        } else {

            $redeem_request_details = RedeemRequest::find($request->redeem_request_id);

            if($redeem_request_details) {

                if($redeem_request_details->status == REDEEM_REQUEST_PAID ) {

                    return back()->with('flash_error' , tr('redeem_request_status_mismatch'));

                } else {

                    $redeem_request_details->paid_amount = $redeem_request_details->paid_amount + $request->paid_amount;

                    $redeem_request_details->status = REDEEM_REQUEST_PAID;

                    $redeem_request_details->save();

                    if ($redeem_request_details->moderator) {

                        $redeem_request_details->moderator->paid_amount += $request->paid_amount;

                        $redeem_request_details->moderator->remaining_amount -= $request->paid_amount;

                        $redeem_request_details->moderator->save();
                    }

                    return back()->with('flash_success' , tr('action_success'));

                }

            } else {
                return back()->with('flash_error' , tr('something_error'));
            }
        }

    }
}
