<?php
	get_header();
?>
<!-- Bootstrap CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/css/bootstrap.min.css" integrity="sha384-PsH8R72JQ3SOdhVi3uxftmaW6Vc51MKb0q5P2rRUpPvrszuE4W1povHYgTpBfshb" crossorigin="anonymous">
    
<!-- Google Fonts -->
<link href="https://fonts.googleapis.com/css?family=Open+Sans:400,600" rel="stylesheet">
<link href="https://fonts.googleapis.com/css?family=Dosis" rel="stylesheet">
<div class="hero-image"></div>
<div class="container">
    <div class="hero-section text-center">
        <div class="hero-text abs-center-elements">
            <h2>James Johnson</h2>
            <h1>Web Developer</h1>
        </div>
        <a class="button hero-button abs-center-elements" href="#past-work-section">See my work</a>
    </div>
    <a id="about-section"></a>
    <div class="section">
        <h2 class="text-center">About</h2>
        <div class="row">
            <div class="col-12 col-xl-6">
                <h3 class="text-center mt-5">Who am I?</h3>
                <img class="headshot mx-auto img-fluid d-block my-4" src="/wp-content/themes/portfolio/images/headshot.png">
                <p class="text-center">
                    I am a senior at Eastern Michigan University, and formerly a web development intern with LawnGuru in Ann Arbor, MI. 
                    I have a passion for creating intuitive, responsive interfaces and fast, lightweight backends.
                </p>
                <div class="row">
                    <div class="col-6">
                        <a href="https://github.com/jnjohnson" target="_blank">
                            <div class="social-link float-right mr-3 mt-4">
                                <img src="/wp-content/themes/portfolio/images/GitHub-Mark-64px.png">
                            </div>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="https://www.linkedin.com/in/james-johnson-jr-ab716a120/" target="_blank">
                            <div class="social-link float-left ml-3 mt-4">
                                <img src="/wp-content/themes/portfolio/images/black-linkedin-64.png">
                            </div>
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-12 col-xl-6">
                <h3 class="text-center mt-5">What are my skills?</h3>
                <div class="mt-4">
                    <h4 class="mb-2">Frontend</h4>
                    <span class="skill-box">AJAX</span>
                    <span class="skill-box">Bootstrap</span>
                    <span class="skill-box">CSS</span>
                    <span class="skill-box">CSS3</span>
                    <span class="skill-box">Handlebars</span>
                    <span class="skill-box">HTML</span>
                    <span class="skill-box">HTML Email</span>
                    <span class="skill-box">HTML 5</span>
                    <span class="skill-box">Javascript</span>
                    <span class="skill-box">JSON</span>
                    <span class="skill-box">Markdown</span>
                    <span class="skill-box">SASS</span>
                    <span class="skill-box">Web Fonts</span>
                </div>
                <div class="mt-4">
                    <h4 class="mb-2">Backend</h4>
                    <span class="skill-box">Express JS</span>
                    <span class="skill-box">MongoDB</span>
                    <span class="skill-box">Mongoose</span>
                    <span class="skill-box">MVC</span>
                    <span class="skill-box">MYSQL</span>
                    <span class="skill-box">Node JS</span>
                </div>
                <div class="mt-4">
                    <h4 class="mb-2">Other Useful Skills</h4>
                    <span class="skill-box">Adobe XD CC</span>
                    <span class="skill-box">Adobe Illustrator CC</span>
                    <span class="skill-box">Adobe Photoshop CC</span>
                    <span class="skill-box">Bash</span>
                    <span class="skill-box">C++</span>
                    <span class="skill-box">Git</span>
                    <span class="skill-box">Gulp</span>
                    <span class="skill-box">Interpersonal Communication</span>
                    <span class="skill-box">Linux</span>
                    <span class="skill-box">Mac OS</span>
                    <span class="skill-box">NPM</span>
                    <span class="skill-box">OOP</span>
                    <span class="skill-box">Public Speaking</span>
                    <span class="skill-box">SEO</span>
                    <span class="skill-box">UX</span>
                    <span class="skill-box">Visual Studio Code</span>
                    <span class="skill-box">Windows</span>
                    <span class="skill-box">Writing</span>
                </div>
            </div>
        </div>
    </div>
</div>
<a id="past-work-section"></a>
<div class="bg-gray">
    <div class="container">
        <div class="section">
            <h2 class="text-center">Past Work</h2>
            <div class="row">
                <div class="col-12">
                    <div class="row work-border">
                        <div class="col-12 col-xl-6">
                            <a class="work-link" href="https://lawnguru.co/" target="_blank">
                                <h4>LawnGuru</h4>
                                <h5>On-demand lawn and snow service</h5>
                            </a>
                            <p>
                                As an intern with LawnGuru, played a significant role in building the new frontend of the Lawnguru.co website. 
                                Also built a new template system for LawnGuru’s HTML Emails.
                            </p>
                            <div class="technologies">
                                <p><small>Technologies</small></p>
                                <span class="technology-box">Bootstrap</span>
                                <span class="technology-box">GIT</span>
                                <span class="technology-box">GITHUB</span>
                                <span class="technology-box">Gulp</span>
                                <span class="technology-box">HTML 5</span>
                                <span class="technology-box">Sass</span>
                            </div>
                        </div>
                        <div class="col-12 col-xl-6">
                            <a href="https://lawnguru.co/" target="_blank">
                                <img class="mx-auto d-block img-fluid" src="/wp-content/themes/portfolio/images/LawnGuru-home.PNG">
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="row work-border">
                        <div class="col-12 col-xl-6">
                            <a class="work-link" href="https://shopping-cart.jnjohnson.io/home" target="_blank">
                                <h4>Shopping Cart</h4>
                                <h5>Ecommerce and inventory management application</h5>
                            </a>
                            <p>
                                As a class project, built this application from a set of specifications, requirements, and designs. 
                                Manages the end-user experience, order history, and administrative inventory control.
                            </p>
                            <div class="technologies">
                                <p><small>Technologies</small></p>
                                <span class="technology-box">Ajax</span>
                                <span class="technology-box">Bootstrap</span>
                                <span class="technology-box">Digital Ocean</span>
                                <span class="technology-box">Handlebars</span>
                                <span class="technology-box">HTML 5</span>
                                <span class="technology-box">Javascript</span>
                                <span class="technology-box">Linux</span>
                                <span class="technology-box">Mean Stack</span>
                                <span class="technology-box">Mongoose</span>
                            </div>
                        </div>
                        <div class="col-12 col-xl-6">
                            <a class="work-link" href="https://shopping-cart.jnjohnson.io/home" target="_blank">
                                <img class="mx-auto d-block img-fluid" src="/wp-content/themes/portfolio/images/Shopping-cart.PNG">
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<a id="contact-section"></a>
<div class="bg-dark-blue">
    <div class="container">
        <div class="contact">
            <h2 class="text-center">Contact</h2>
            <form method="post" href="mailto:jimmy@jnjohnson.io" class="contact-form">
                <input class="form-item mx-auto d-block py-1" type="text" placeholder="Name">
                <input class="form-item mx-auto d-block py-1" type="email" placeholder="Email">
                <textarea class="form-item mx-auto d-block py-1" placeholder="Your Message"></textarea>
                <div class="submit-button-container mx-auto d-block">
                    <button class="button submit-button" type="submit">SUBMIT</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.3/umd/popper.min.js" integrity="sha384-vFJXuSJphROIrBnz7yo7oB41mKfc8JzQZiCq4NCceLEaO4IHwicKwpJf9c9IpFgh" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/js/bootstrap.min.js" integrity="sha384-alpBpkh1PFOepccYVYDB4do5UnbKysX5WZXm3XxPqe5iKTfUKjNkCk9SaVuEZflJ" crossorigin="anonymous"></script>
<?php
	get_footer();