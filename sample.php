<!DOCTYPE html>
<html lang="en"><head>
<meta http-equiv="content-type" content="text/html; charset=windows-1252">
	<title>Sample Page</title>

<!-- Character Encoding -->
<meta charset="UTF-8" />

<!-- Viewport & Theme -->
<meta name="viewport" content="width=device-width, initial-scale=1" />
<meta name="theme-color" content="#369" />

<!-- Standard Meta -->
<meta name="author" content="Clackmannanshire Council" />
<meta name="description" content="Information about access to information under Freedom of Information legislation." />

<!-- Dublin Core Namespace Declarations -->
<link rel="schema.DC" href="http://purl.org/dc/elements/1.1/" />
<link rel="schema.DCTERMS" href="http://purl.org/dc/terms/" />

<!-- Dublin Core Metadata -->
<meta name="DC.language" content="en" />
<meta name="DCTERMS.created" content="2004-11-02" />
<meta name="DCTERMS.modified" content="2024-11-05" />
<meta name="DC.creator" content="Senior Governance Officer, Clackmannanshire Council" />
<meta name="DC.publisher" content="Clackmannanshire Council" />
<meta name="DC.title" content="Access to Information" />
<meta name="DC.description" content="Sample Page" />
<meta name="DC.subject" content="sample, page" />
<meta name="DC.rights" content="https://www.nationalarchives.gov.uk/doc/open-government-licence/" />

<!-- Open Graph -->
<meta property="og:title" content="Sample Page" />
<meta property="og:description" content="Sample Page" />
<meta property="og:url" content="/" />
<meta property="og:type" content="website" />

<meta name="facebook-domain-verification" content="dyv48zg4ngo7us3b8jy7i3qtgjgt8y" />

<link rel='shortcut icon' href='/favicon.ico' type='image/x-icon'/>
<link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon-180x180.png" />
<link rel="schema.DC" href="http://purl.org/dc/elements/1.1/" />
<link rel="schema.DCTERMS" href="http://purl.org/dc/terms/" />

<!--__________________________________________________________________________________________________________________________________________________-->
    <!-- Bootstrap core CSS -->
    <link href="/css/bootstrap/bootstrap.css" rel="stylesheet">
		<!-- Custom styles for this template -->
		<link href="/css/bootstrap/check.css" rel="stylesheet">
    		<link href="/css/bootstrap/jumbotron.css" rel="stylesheet">

     	<!-- Original ClacksWeb styles for this template -->
    	<link rel="stylesheet" type="text/css" media="screen" href="/css/screenbs.css">
	<link rel="stylesheet" type="text/css" media="screen" href="/css/standardbs.css">
<!--__________________________________________________________________________________________________________________________________________________-->

<!-- ===================== CHATBOT WIDGET STYLES ===================== -->
<style>

  /* --- Wrapper anchors everything to bottom-right --- */
  .chatbot-widget {
    position: fixed;
    bottom: 24px;
    right: 24px;
    z-index: 9999;
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 12px;
  }

  /* --- Chat panel (sits above the button) --- */
  .chatbot-panel {
    width: 380px;
    height: 560px;
    background: #ffffff;
    border-radius: 16px;
    box-shadow: 0 8px 40px rgba(0, 0, 0, 0.2);
    display: flex;
    flex-direction: column;
    overflow: hidden;

    /* Hidden by default — animates in */
    opacity: 0;
    transform: translateY(16px) scale(0.97);
    pointer-events: none;
    transition: opacity 0.22s ease, transform 0.22s ease;
    transform-origin: bottom right;
  }

  .chatbot-panel.is-open {
    opacity: 1;
    transform: translateY(0) scale(1);
    pointer-events: all;
  }

  /* --- Panel header --- */
  .chatbot-panel-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    background-color: #336699;
    color: #ffffff;
    padding: 14px 16px;
    flex-shrink: 0;
  }

  .chatbot-panel-header h2 {
    margin: 0;
    font-size: 1rem;
    font-weight: 600;
    color: #ffffff;
  }

  .chatbot-close-btn {
    background: none;
    border: none;
    color: #ffffff;
    font-size: 1.4rem;
    line-height: 1;
    cursor: pointer;
    padding: 4px;
    border-radius: 4px;
    min-height: 44px;
    min-width: 44px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background-color 0.18s ease;
  }

  .chatbot-close-btn:hover {
    background-color: rgba(255, 255, 255, 0.2);
  }

  .chatbot-close-btn:focus-visible {
    outline: 3px solid #ffffff;
    outline-offset: 2px;
  }

  /* --- Iframe fills the rest of the panel --- */
  .chatbot-iframe {
    display: block;
    border: none;
    width: 100%;
    flex: 1;
  }

  /* --- Trigger button --- */
  .chatbot-trigger {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    min-height: 53px;
    padding: 12px 24px;
    background-color: #ffda00;
    color: #000;
    font-size: 1.2rem;
    font-weight: 600;
    font-family: inherit;
    text-decoration: none;
    white-space: nowrap;
    border: 2px solid #336699;
    border-radius: 999px;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.2);
    cursor: pointer;
    transition: background-color 0.18s ease, color 0.18s ease, transform 0.18s ease, box-shadow 0.18s ease;
  }

  .chatbot-trigger:hover {
    background-color: #81a7cc;
    color: #fff;
    text-decoration: none;
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.25);
  }

  .chatbot-trigger:active {
    background-color: #81a7cc;
    color: #fff;
    transform: translateY(0);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
  }

  .chatbot-trigger:focus-visible {
    outline: 3px solid #336699;
    outline-offset: 4px;
    border-radius: 999px;
  }

  /* --- Utility --- */
  .sr-only {
    position: absolute;
    width: 1px;
    height: 1px;
    padding: 0;
    margin: -1px;
    overflow: hidden;
    clip: rect(0, 0, 0, 0);
    white-space: nowrap;
    border-width: 0;
  }

  @media (prefers-reduced-motion: reduce) {
    .chatbot-panel,
    .chatbot-trigger,
    .chatbot-close-btn {
      transition: none;
    }
  }

  /* Mobile — panel goes full screen */
  @media (max-width: 480px) {
    .chatbot-widget {
      bottom: 0;
      right: 0;
      left: 0;
      align-items: center;
      padding-bottom: 16px;
    }

    .chatbot-panel {
      width: 100vw;
      height: 85vh;
      border-radius: 16px 16px 0 0;
      transform-origin: bottom center;
    }

    .chatbot-trigger {
      font-size: 1rem;
      min-height: 48px;
    }
  }
</style>
<!-- ================================================================ -->

</head>
<body>


<div class="off-left">
	<a href="#content" title="Skip to main content." id="top">Skip to main content</a> |
 <a href="#sectionnav">Skip to section menu</a> | 	<a href="http://www.clacks.gov.uk/site/accesskeys/" title="Access keys.">Access keys</a>.
 	<h1>Clackmannanshire Council Online</h1>
  </div>


<!--____________________________________________________Header Start______________________________________________________________________________________________-->
  <div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
  <div class="container" style="padding:0 5px 0 5px;">
    <div class="navbar-header">
      <button class="navbar-toggle" type="button" data-toggle="collapse" data-target=".bs-navbar-collapse">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand" href="http://www.clacks.gov.uk/"><strong>Clackmannanshire</strong> Council</a>
    </div>
    <nav class="collapse navbar-collapse bs-navbar-collapse navbar-right" role="navigation">
      <ul class="nav navbar-nav">
        <li>
          	<a href="http://www.clacks.gov.uk/site/atoz/">A-Z</a>
        </li>
        <li>
          	<a href="http://www.clacks.gov.uk/site/contact/">Contact</a>
        </li>
         <li>
		<a href="/myaccount/">MyAccount</a>
        </li>
      </ul>
		<form class="navbar-form navbar-right" role="form" action="/searchresults.php">
            <div class="form-group">
    			<input name="cx" value="003785486861710258727:zojhh_daw0a" type="hidden">
    			<input name="cof" value="FORID:10" type="hidden">
    			<input name="ie" value="UTF-8" type="hidden">
     			<input name="bootstrap" value="true" type="hidden">
    			<label id="qsearch" for="q">Search</label>
    			<input onfocus="this.select();" class="form-control" id="q" name="q" placeholder="Search" onclick="this.value='';" type="text">
            </div>
        </form>
    </nav>
  </div>
</div>
<!--____________________________________________________Header End______________________________________________________________________________________________-->


<!--____________________________________________________Homepage Code______________________________________________________________________________________________-->

<div class="container">
	<div class="row" style="margin-top:25px;">
        <div class="hidden-xs hidden-sm col-md-13">

	<div id="sitenav">
		<!--__________________________________________________________________________________________________________________________________________________-->
			<h2 id="sections">Payments</h2>
			<ul id="sectionmenu">
					<li><a class ="current color-xs" href="/bulkyuplift/index.php">Apply Online</a></li>
					<li><a href="/bulkyuplift/faqs.php">Payment FAQs</a></li>
					<li><a href="/bulkyuplift/contact.php">Contact Us</a></li>
					<li><a href="http://www.clacks.gov.uk/environment/wasteserviceshome/">Waste Home</a></li>
			</ul>
	</div>
		<!--sitenav-->

	</div>
	<!--col-md-13 -->

<div class="col-md-14 contentpadding">
	<h2>Sample Heading</h2>
	<p>Sample Text</p>
</div>
	<!--col-md-14 -->

 	<div class="col-md-13">
 		<div id="sectionnav" class="color-xs"><h2 id="toptaskhead" class="color-xs">Security Information</h2>
 			<ul id="sectionmenulower" class="color-xs">
 				<li>
	 				<p>Your transaction is now taking place within a secure payment system.</p>
					<p>We use Secure Socket Layer (SSL) technology, a standard supported by all major Internet browsers.</p>
					<p>All of your personal and card details are protected using 128 bit encryption, when transmitted over the Internet.</p>
 				</li>
			</ul>
		</div>
	</div>
	<!--col-md-13 -->

	</div>
	<!-- row -->

	</div>
	<!-- container -->


	      <div class="fullfooter" style="margin-top:35px; background-color:#ffda00;padding-top:0px;padding-bottom:0px;">
	      <div class="container" style="padding:0px;">
	      <footer class="footerupper">
	      		<div class="row marketing" style="margin:0px;">
				<div class="col-sm-3">
					<p class="footerupper" style="color:#fff;"><strong>A to Z of services</strong></p>
			     	</div>
			     	<div class="col-sm-9">
			     		<p class="footerupper">
					<a href="http://www.clacks.gov.uk/site/atoz/?a=a&amp;e=c">A</a>
					<a href="http://www.clacks.gov.uk/site/atoz/?a=b&amp;e=c">B</a>
        				<a href="http://www.clacks.gov.uk/site/atoz/?a=c&amp;e=c">C</a>
        				<a href="http://www.clacks.gov.uk/site/atoz/?a=d&amp;e=c">D</a>
        				<a href="http://www.clacks.gov.uk/site/atoz/?a=e&amp;e=c">E</a>
   	    				<a href="http://www.clacks.gov.uk/site/atoz/?a=f&amp;e=c">F</a>
        				<a href="http://www.clacks.gov.uk/site/atoz/?a=g&amp;e=c">G</a>
        				<a href="http://www.clacks.gov.uk/site/atoz/?a=h&amp;e=c">H</a>
        				<a href="http://www.clacks.gov.uk/site/atoz/?a=i&amp;e=c">I</a>
   	    				<a href="http://www.clacks.gov.uk/site/atoz/?a=j&amp;e=c">J</a>
        				<a href="http://www.clacks.gov.uk/site/atoz/?a=k&amp;e=c">K</a>
        				<a href="http://www.clacks.gov.uk/site/atoz/?a=l&amp;e=c">L</a>
        				<a href="http://www.clacks.gov.uk/site/atoz/?a=m&amp;e=c">M</a>
        				<a href="http://www.clacks.gov.uk/site/atoz/?a=n&amp;e=c">N</a>
        				<a href="http://www.clacks.gov.uk/site/atoz/?a=o&amp;e=c">O</a>
        				<a href="http://www.clacks.gov.uk/site/atoz/?a=p&amp;e=c">P</a>
	    				Q&nbsp;
        				<a href="http://www.clacks.gov.uk/site/atoz/?a=r&amp;e=c">R</a>
        				<a href="http://www.clacks.gov.uk/site/atoz/?a=s&amp;e=c">S</a>
        				<a href="http://www.clacks.gov.uk/site/atoz/?a=t&amp;e=c">T</a>
        				<a href="http://www.clacks.gov.uk/site/atoz/?a=u&amp;e=c">U</a>
        				<a href="http://www.clacks.gov.uk/site/atoz/?a=v&amp;e=c">V</a>
        				<a href="http://www.clacks.gov.uk/site/atoz/?a=w&amp;e=c">W</a>
	    				X&nbsp;
        				<a href="http://www.clacks.gov.uk/site/atoz/?a=y&amp;e=c">Y</a>
	    				Z</p>
			     	</div>
				</div>
	      </footer>
	     </div>
    </div>

	<!-- container -->
	<div class="fullfooter">
      <div class="container" style="padding:0px;">
      <footer class="footerlower">
      		<div class="row marketing">
      			<div class="col-sm-6">

					<p><strong>&copy; Copyright <?php echo date("Y"); ?> Clackmannanshire Council</strong>
						<br>Kilncraigs, Alloa, Clackmannanshire, FK10 1EB<br><br>Tel: 01259 450000<br>Email: <a href="mailto:customerservice@clacks.gov.uk">customerservice@clacks.gov.uk</a>
					</p>
					<p>
						<a class="footer-brand" href="http://www.clacks.gov.uk/"><strong>Clackmannanshire</strong> Council</a>
					</p>
		     	</div>
      			<div class="col-sm-6 alignright">
        		<ul>
				<li><a href="https://www.clacks.gov.uk/site/atoz/">A - Z</a></li>
     				<li><a href="https://www.clacks.gov.uk/site/changelog/">Change Log</a></li>
      				<li><a href="https://www.clacks.gov.uk/council/commentsandcomplaints/">Comments &amp; complaints</a></li>
      				<li><a href="https://www.clacks.gov.uk/site/contact/">Contact Us</a></li>
				<li><a href="https://www.clacks.gov.uk/site/privacy/" accesskey="8">Cookies &amp; Privacy</a></li>
				<li><a href="https://www.clacks.gov.uk/regulation/dataprotectionpolicy/">Data Protection</a></li>
				<li><a href="https://www.clacks.gov.uk/site/disclaimer/">Disclaimer</a></li>
				<li><a href="https://www.clacks.gov.uk/council/eqopps/">Equality &amp; Diversity</a></li>
				<li><a href="https://www.clacks.gov.uk/site/help/" accesskey="6">Help</a></li>
				<li><a href="https://www.clacks.gov.uk/searchresults.php?bootstrap=true" accesskey="4">Search</a></li>
				<li><a href="https://www.clacks.gov.uk/site/webaccessibility/" accesskey="0">Web Accessibility</a></li>
			</ul>
				<p><a href="#top">Back to top ^</a></p>
		     	</div>
			</div>
      </footer>
     </div>
     <!-- container -->
    </div>
     <!-- jumbotron -->

<!--__________________________________________________________________________________________________________________________________________________-->
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
	<script src="/js/bootstrap/bootstrap.min.js"></script>
<!--__________________________________________________________________________________________________________________________________________________-->

<!-- ===================== CHATBOT WIDGET ===================== -->
<div class="chatbot-widget" id="chatbotWidget">

  <!-- Chat panel — appears above the button -->
  <div
    class="chatbot-panel"
    id="chatbotPanel"
    role="dialog"
    aria-modal="true"
    aria-labelledby="chatbotTitle"
  >
    <div class="chatbot-panel-header">
      <h2 id="chatbotTitle">Chat with us</h2>
      <button
        class="chatbot-close-btn"
        aria-label="Close chat"
        onclick="closeChatbot()"
      >&#x2715;</button>
    </div>

    <!-- Replace src with your actual Copilot Studio embed URL -->
    <iframe
      id="chatbotFrame"
      src="about:blank"
      title="Clackmannanshire Council Customer Chatbot"
      class="chatbot-iframe"
      allow="microphone"
    ></iframe>
  </div>

  <!-- Trigger link -->
  <a
    href="https://your-chatbot-url.com"
    class="chatbot-trigger"
    id="chatbotTrigger"
    aria-label="Open chat with us"
    aria-haspopup="dialog"
    aria-expanded="false"
    role="button"
    onclick="openChatbot(); return false;"
  >
    <svg
      aria-hidden="true"
      focusable="false"
      xmlns="http://www.w3.org/2000/svg"
      width="22"
      height="22"
      viewBox="0 0 24 24"
      fill="none"
      stroke="currentColor"
      stroke-width="2"
      stroke-linecap="round"
      stroke-linejoin="round"
    >
      <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
    </svg>
    <span>Chat with us</span>
  </a>

</div>

<script>
  var _chatOpen = false;
  var _chatbotURL = 'https://copilotstudio.microsoft.com/environments/9663f5fa-1599-e28c-8607-d41673845da8/bots/crf89_agentWpoAMN/webchat?__version__=2%22'; //

  function openChatbot() {
    _chatOpen = true;
    // Lazy-load the iframe only on first open
    var frame = document.getElementById('chatbotFrame');
    if (frame.src === 'about:blank' || frame.src === '') {
      frame.src = _chatbotURL;
    }
    var panel = document.getElementById('chatbotPanel');
    panel.classList.add('is-open');
    var trigger = document.getElementById('chatbotTrigger');
    trigger.setAttribute('aria-expanded', 'true');
    // Focus the close button so screen readers announce the dialog
    document.querySelector('.chatbot-close-btn').focus();
    document.addEventListener('keydown', _chatKeyHandler);
  }

  function closeChatbot() {
    _chatOpen = false;
    var panel = document.getElementById('chatbotPanel');
    panel.classList.remove('is-open');
    var trigger = document.getElementById('chatbotTrigger');
    trigger.setAttribute('aria-expanded', 'false');
    document.removeEventListener('keydown', _chatKeyHandler);
    // Return focus to trigger link
    trigger.focus();
  }

  function _chatKeyHandler(e) {
    // Escape closes the panel (WCAG 2.1.2)
    if (e.key === 'Escape') {
      closeChatbot();
      return;
    }
    // Focus trap within the panel (WCAG 2.4.3)
    if (e.key === 'Tab') {
      var panel = document.getElementById('chatbotPanel');
      var focusable = panel.querySelectorAll(
        'button, iframe, [tabindex]:not([tabindex="-1"])'
      );
      var first = focusable[0];
      var last = focusable[focusable.length - 1];
      if (e.shiftKey) {
        if (document.activeElement === first) {
          e.preventDefault();
          last.focus();
        }
      } else {
        if (document.activeElement === last) {
          e.preventDefault();
          first.focus();
        }
      }
    }
  }
</script>
<!-- =========================================================== -->

</body>
</html>