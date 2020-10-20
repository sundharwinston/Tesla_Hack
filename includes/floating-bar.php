<!-- feedback -->
<div class="feedback" id="feedback">
    <a href="#feedback-form">Feedback</a>
</div>

<div id="feedback-form" class="overlay">
	<div class="popup">
        <div class="div1" style="background:#fff">
            
            <a class="close" href="#">×</a>
        
            <div class="content quickenquire" id="quickenquire">
                <h3 style="text-align:center">Feedback</h3>
                <?php
                    $feedback=new Feedback();
                    $feedback->Register(); 
                ?>
                <form id="feedback-block" method="post" action="">
                    <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" class="form-control" name="feedbackname" placeholder="Your Name" value="<?php echo $feedback->ReadInput('feedbackname'); ?>" />
                    </div>
                    <div class="form-group">
                    <label for="email">Email Id</label>
                    <input type="text" class="form-control" name="feedbackemail" placeholder="Your Email Address" value="<?php echo $feedback->ReadInput('feedbackemail'); ?>" />
                    </div>
                    <div class="form-group">            
                    <label for="comment">Subject</label>
                    <input type="text" class="form-control" name="feedbacksubject" placeholder="Subject" value="<?php echo $feedback->ReadInput('feedbacksubject'); ?>" />
                    </div>
                    <div class="form-group">
                    <label for="contact">Phone number</label>
                    <input type="text" class="form-control" name="feedbackphonenumber" value="<?php echo $feedback->ReadInput('feedbackphoneNumber'); ?>" />
                    <input type="hidden" name="feedbackphoneprefix" value="<?php echo $feedback->ReadInput('feedbackphoneprefix'); ?>" />
                    </div>
                    <div class="form-group">        
                    <label for="country">Your comments</label>
                    <textarea placeholder="Your Comments" class="form-control" name="feedbackcomments" rows="3"><?php echo $feedback->ReadInput('feedbackcomments'); ?></textarea>
                    </div>
                    <div class="form-group">
                        <div class="input-group">
                        <input type="text" name="feedbackcaptcha" placeholder="Enter the code">
                            <span class="input-group-addon no-padding">
                                <img id="feedback-capcha" src="<?php echo ABS_URL; ?>capcha/?k=feedback&s=125x32&l=6&f=-1" /><a class="capcha" img-href="<?php echo ABS_URL; ?>capcha/?k=feedback&s=125x32&l=6&f=-1" title="Click here to refresh the code" data-target="#feedback-capcha"><i class="fa fa-refresh" ></i></a>
                            </span>
                        </div>
                    </div>
                    <div class="form-group">
                    <input type="submit" value="Submit">
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<!-- get a call -->

<div class="feedback feedback2" id="feedback2">
	<a href="#getacall-form">Get a call</a>
</div>


<div id="getacall-form" class="overlay">
	<div class="popup">
        <div class="div1" style="background:#fff">
            
            <a class="close" href="#">×</a>
        
            <div class="content quickenquire" id="quickenquire">
                <h3 style="text-align:center">Get a call</h3>
		        <?php $getacall=new GetACall();?>
            	<?php $getacall->Register(); ?>
                <form id="getcall-block" method="post" action="">
                    <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" class="form-control" name="getacallname" placeholder="Your Name" value="<?php echo $getacall->ReadInput('getacallname'); ?>" />
                    </div>
                    <div class="form-group">
                    <label for="email">Email Id</label>
                    <input type="text" class="form-control" name="getacallemail" placeholder="Your Email" value="<?php echo $getacall->ReadInput('getacallemail'); ?>" />
                    </div>
                    <div class="form-group">
                    <label for="contact">Phone number</label>
                    <input type="text" class="form-control" name="getacallphonenumber" value="<?php echo $getacall->ReadInput('getacallphonenumber'); ?>" />
                    <input type="hidden" name="getacallphoneprefix" value="<?php echo $getacall->ReadInput('getacallphoneprefix'); ?>" />
                    </div>
                    <div class="form-group">
                    <label for="ctc">Convenient time to call</label>
                    <input type="text" class="form-control" name="getacalltimetocall" placeholder="11 AM to 02 PM" value="<?php echo $getacall->ReadInput('getacalltimetocall'); ?>" />
                    </div>
                    <div class="form-group">
                    <label for="yc">Your comments</label>
                    <textarea class="form-control" name="getacallcomments" placeholder="Your Comments" rows="3"><?php echo $getacall->ReadInput('getacallcomments'); ?></textarea>
                    </div>
                    <div class="form-group">
                        <div class="input-group">
                        <input type="text" name="getacallcaptcha" placeholder="Enter the code">
                            <span class="input-group-addon no-padding">
                                <img id="getacall-capcha" src="<?php echo ABS_URL; ?>capcha/?k=getacall&s=125x32&l=6&f=-1" /><a class="capcha" img-href="<?php echo ABS_URL; ?>capcha/?k=getacall&s=125x32&l=6&f=-1" title="Click here to refresh the code" data-target="#getacall-capcha"><i class="fa fa-refresh" ></i></a>
                            </span>
                        </div>
                    </div>
                    <div class="form-group">
		            <input type="submit" value="Submit">
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
