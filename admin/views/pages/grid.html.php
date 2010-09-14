<div class="flash-message">Test Flash Standard message</div> <!-- ORANGE -->
<div class="flash-message pass">Test Flash Pass message</div> <!-- GREEN -->
<div class="flash-message fail">Test Flash Fail message</div> <!-- RED -->
<div class="flash-message notice">Test Flash Notice message</div> <!-- BLUE -->
<div class="flash-message warning">Test Flash Warning message</div> <!-- YELLOW -->

			<div class="grid_16">
				<h2 id="page-heading">Template Examples for Rapid Prototyping in Lithium</h2>
			</div>
			
			<div class="clear"></div>
			
			<div class="grid_4">
				<?php echo $this->view()->render(array('element' => '../elements/box'), array(					
					'boxtitle' => 'This is an Element',
					'boxbody' => "These boxes, are elements. You can easily add them to your pages by adding an include element line. And the best part, you dont even have to copy them over to your elements directory. They are setup to use variables for the title and body. So all you have to do is pass those to the element and you have instant content"
				)); ?>
			</div>
			
			<div class="grid_4">
				<?php echo $this->view()->render(array('element' => '../elements/box'), array(					
					'boxtitle' => 'How to Pass',
					'boxbody' => "To pass the title of the element, just store it into the 'boxtitle' key value in the element. And 'boxbody' for the body. You can then easily fill these values with content from a controller and place that variables identifier into the value field for the boxtitle and boxbody keys. If you do not want to pass the variables but want to just add the text into your element. Simply copy box.html.php element from li3_grid/views/elements to your own elements directories and edit as you desire."
				)); ?>
			</div>
			
			<div class="grid_4">
				<?php echo $this->view()->render(array('element' => '../elements/box'), array(					
					'boxtitle' => 'Flexibility',
					'boxbody' => 'Most boxes you see on this page, are rendered using one single box element. Thats what they are for. For the more complex boxes, all you have to do is store the html into a variable and echo that variable using <strong>&lt;?=$varname?&gt;</strong>. You have to use that syntax so all the special characters are escaped such as \' and " so it does not break the box.'
				)); ?>
			</div>
			
			<div class="grid_4">
				<?php echo $this->view()->render(array('element' => '../elements/box'), array(					
					'boxtitle' => 'Contribution',
					'boxbody' => 'If you have anything to add or would like to give some input, please follow the project on http://github.com/luckysmack/li3_grid and file an issue. If you would like to add support for your library or lithium plugin you can either create an issue with what you are looking for and some code to possibly help. Or you can fork the project and add your code and submit a pull request. I would like this so others can benefit from your integration.'
				)); ?>
			</div>

			<div class="clear"></div>
			
			<div class="grid_16">
				<!--- 
					To add a kwik to your page, use the element line below and edit it to name only the file name
					And move the kwik.html.php to you local elements dir.
				-->
				<?php echo $this->view()->render(array('element' => '../elements/kwik')); ?>
			</div>
			
			<div class="clear"></div>
			
			
			<div class="grid_4">
				
				<div class="box">
					<h2>
						<a href="#" id="toggle-paragraphs">Paragraphs</a>
					</h2>
					<div class="block" id="paragraphs">
						<p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat.</p>
						<p>Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat.</p>
					</div>
				</div>
				
				<div class="box">
					<h2>
						<a href="#" id="toggle-ajax" class="hidden"><img src="img/ajax-loader.gif" id="loading" alt="Loading" />
							Ajax Content
						</a>
					</h2>
					<div class="block" id="ajax-content">
						Content loaded dynamically
					</div>
				</div>
				
				<div class="box menu">
					<h2>
						<a href="#" id="toggle-section-menu">Section Menu</a>
					</h2>
					<div class="block" id="section-menu">
						<ul class="section menu">
							<li>
								<a class="menuitem">Menu 1</a>
								<ul class="submenu">
									<li>
										<a>Submenu 1</a>
									</li>
									<li>
										<a>Submenu 2</a>
									</li>
									<li>
										<a class="active">Submenu 3</a>
									</li>
									<li>
										<a>Submenu 4</a>
									</li>
									<li>
										<a>Submenu 5</a>
									</li>
								</ul>
							</li>
							<li>
								<a class="menuitem">Menu 2</a>
								<ul class="submenu">
									<li>
										<a>Submenu 1</a>
									</li>
									<li>
										<a>Submenu 2</a>
									</li>
									<li>
										<a>Submenu 3</a>
									</li>
									<li>
										<a>Submenu 4</a>
									</li>
									<li>
										<a>Submenu 5</a>
									</li>
								</ul>
							</li>
							<li>
								<a class="menuitem">Menu 3</a>
								<ul class="submenu">
									<li>
										<a>Submenu 1</a>
									</li>
									<li>
										<a>Submenu 2</a>
									</li>
									<li>
										<a>Submenu 3</a>
									</li>
									<li>
										<a>Submenu 4</a>
									</li>
									<li>
										<a>Submenu 5</a>
									</li>
								</ul>
							</li>
							<li>
								<a class="menuitem">Menu 4</a>
								<ul class="submenu">
									<li>
										<a>Submenu 1</a>
									</li>
									<li>
										<a>Submenu 2</a>
									</li>
									<li>
										<a>Submenu 3</a>
									</li>
									<li>
										<a>Submenu 4</a>
									</li>
									<li>
										<a>Submenu 5</a>
									</li>
								</ul>
							</li>
						</ul>
					</div>
				</div>
				<div class="box">
					<h2>
						<a href="#" id="toggle-list-items">List Items</a>
					</h2>
					<div class="block" id="list-items">
						<p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod. </p>
						<h5>A menu</h5>
						<ul class="menu">
							<li>
								<a href="#">Item 1</a>
							</li>
							<li>
								<a href="#">Item 2</a>
							</li>
							<li>
								<a href="#">Item 3</a>
							</li>
						</ul>
						<h5>An unordered list</h5>
						<ul>
							<li>
								<a href="#">Item 1</a>
							</li>
							<li>
								<a href="#">Item 2</a>
							</li>
							<li>
								<a href="#">Item 3</a>
							</li>
						</ul>
						<h5>An ordered list</h5>
						<ol>
							<li>
								<a href="#">Item 1</a>
							</li>
							<li>
								<a href="#">Item 2</a>
							</li>
							<li>
								<a href="#">Item 3</a>
							</li>
						</ol>
					</div>
				</div>
			</div>
			<div class="grid_7">
				<div class="box">
					<h2>
						<a href="#" id="toggle-accordion">Accordion</a>
					</h2>
					<div class="block" id="accordion">
						<div id="accordion">
							<h3 class="toggler atStart">Design Process</h3>
							<div class="element atStart">
								<h4>The Value of Design</h4>
								<p>Design is based on the inspiration of past accomplishments. On that foundation, we can build upon those achievements to shape the future. Design is about life — past, present and future — and the learning process that happens between birth and death. It is about community and shared knowledge and experience. It is the passion to build on what we've learned to create something better.</p>
							</div>
							<h3 class="toggler atStart">Design Influences</h3>
							<div class="element atStart">
								<h4>About this Site</h4>
								<p>The words "design influences" can be understood as both a plural noun and as subject and verb. The plural noun speaks of those who have come before us and paved the way. The verb speaks of the responsibility of design to lead the way. By understanding where we have come from, we have a better idea of where we are going and, perhaps, where we should be heading.</p>
							</div>
							<h3 class="toggler atStart">Inspiration</h3>
							<div class="element atStart">
								<h4>Inspired by the Past</h4>
								<p>I have been inspired by the work of many who have pioneered advances in Web Standards, including 
						<a href="http://www.31three.com/" title="www.31three.com">Jesse Bennett-Chamberlain</a>, 
						<a href="http://www.stopdesign.com/" title="www.stopdesign.com">Douglas Bowman</a>, 
						<a href="http://www.chaoticpattern.com/" title="www.chaoticpattern.com">Allen Chang</a>, 
						<a href="http://www.stuffandnonsense.co.uk/" title="www.stuffandnonsense.co.uk">Andy Clarke</a>, 
						<a href="http://www.hicksdesign.co.uk/" title="www.hicksdesign.co.uk">Jon Hicks</a>, 
						<a href="http://www.shauninman.com/" title="www.shauninman.com">Shaun Inman</a>, 
						<a href="http://www.cameronmoll.com/" title="www.cameronmoll.com">Cameron Moll</a>, 
						<a href="http://veerle.duoh.com/" title="veerle.duoh.com">Veerle Pieters</a>, 
						<a href="http://www.jasonsantamaria.com/" title="www.jasonsantamaria.com">Jason Santa Maria</a>, 
						<a href="http://www.mezzoblue.com/" title="www.mezzoblue.com">Dave Shea</a>, 
						<a href="http://www.thebignoob.com/" title="www.thebignoob.com">Ryan Sims</a>, 
						<a href="http://www.sonspring.com/" title="www.sonspring.com">Nathan Smith</a>, and 
						<a href="http://www.zeldman.com/" title="www.zeldman.com">Jeffrey Zeldman</a>, 
						to name a few. Thank you for inspiring <a href="http://www.domain7.com/WhoWeAre/StephenBau.html" title="Stephen Bau, Designer, Domain7">me</a> to give something back.</p>
							</div>
							<h3 class="toggler atStart">Contribution</h3>
							<div class="element atStart">
								<h4>Shaping the Future</h4>
								<p>The <a href="/fluid960gs/">Fluid 960 Grid System</a> templates have been built upon the work of Nathan Smith and his <a href="http://960.gs/">960 Grid System</a> using effects from the <a href="http://www.mootools.net/">Mootools</a> JavaScript library. The idea for building these templates was inspired by Andy Clarke, author of <a href="http://www.transcendingcss.com/">Transcending CSS</a>, who advocates a content-out approach to rapid interactive prototyping, crediting Jason Santa Maria with the <a href="http://www.jasonsantamaria.com/archive/2004/05/24/grey_box_method.php">grey box method</a>.</p>
							</div>
						</div>
					</div>
				</div>
				<div class="box">
					<h2>
						<a href="#" id="toggle-blockquote">Blockquote</a>
					</h2>
					<div class="block" id="blockquote">
						<blockquote>
							<p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat.</p>
							<p class="cite">
								<cite>John Smith</cite>
							</p>
						</blockquote>
					</div>
				</div>
				<div class="box">
					<h2>
						<a href="#" id="toggle-tables">Tables</a>
					</h2>
					<div class="block" id="tables">
						<table>
							<tr>
								<th>Lorem ipsum</th>
								<td>Dolor sit</td>
								<td class="currency">$125.00</td>
							</tr>
							<tr>
								<th>Dolor sit</th>
								<td>Nostrud exerci</td>
								<td class="currency">$75.00</td>
							</tr>
							<tr>
								<th>Nostrud exerci</th>
								<td>Lorem ipsum</td>
								<td class="currency">$200.00</td>
							</tr>
							<tr>
								<th>Lorem ipsum</th>
								<td>Dolor sit</td>
								<td class="currency">$64.00</td>
							</tr>
							<tr>
								<th>Dolor sit</th>
								<td>Nostrud exerci</td>
								<td class="currency">$36.00</td>
							</tr>
						</table>
						<table summary="This table includes examples of as many table elements as possible">
							<caption>An example table</caption>
							<colgroup>
								<col class="colA" />
								<col class="colB" />
								<col class="colC" />
							</colgroup>
							<thead>
								<tr>
									<th colspan="3" class="table-head">Table heading</th>
								</tr>
								<tr>
									<th>Column 1</th>
									<th>Column 2</th>
									<th class="currency">Column 3</th>
								</tr>
							</thead>
							<tfoot>
								<tr>
									<th>Subtotal</th>
									<td></td>
									<th class="currency">$500.00</th>
								</tr>
								<tr class="total">
									<th>Total</th>
									<td></td>
									<th class="currency">$500.00</th>
								</tr>
							</tfoot>
							<tbody>
								<tr class="odd">
									<th>Lorem ipsum</th>
									<td>Dolor sit</td>
									<td class="currency">$125.00</td>
								</tr>
								<tr>
									<th>Dolor sit</th>
									<td>Nostrud exerci</td>
									<td class="currency">$75.00</td>
								</tr>
								<tr class="odd">
									<th>Nostrud exerci</th>
									<td>Lorem ipsum</td>
									<td class="currency">$200.00</td>
								</tr>
								<tr>
									<th>Lorem ipsum</th>
									<td>Dolor sit</td>
									<td class="currency">$64.00</td>
								</tr>
								<tr class="odd">
									<th>Dolor sit</th>
									<td>Nostrud exerci</td>
									<td class="currency">$36.00</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
				<div class="box">
					<h2>
						<a href="#" id="toggle-forms">Forms</a>
					</h2>
					<div class="block" id="forms">
						<form action="">
							<fieldset class="login">
								<legend>Login Information</legend>
								<p>
									<label>Username: </label>
									<input type="text" name="username" value="" />
								</p>
								<p>
									<label>Password: </label>
									<input type="password" name="password" />
								</p>
								<p>
									<label>Re-type Password: </label>
									<input type="password" name="password2" />
								</p>
								<input class="confirm button" type="submit" value="Confirm Availability" />
							</fieldset>
							<fieldset>
								<legend>Personal Information</legend>
								<p>
									<label>First Name: </label>
									<input type="text" name="first-name" value="" />
								</p>
								<p>
									<label>Last Name: </label>
									<input type="text" name="last-name" value="" />
								</p>
								<p>
									<label>Address: </label>
									<input type="text" name="address1" value="" />
								</p>
								<p>
									<label>Address 2: </label>
									<input type="text" name="address2" value="" />
								</p>
								<p>
									<label>City: </label>
									<input type="text" name="city" value="" />
								</p>
								<p>
									<label>State/Province: </label>
									<select name="State">
										<option value="">Select State/Province...</option>
										<option value="AL">Alabama </option>
										<option value="AK">Alaska </option>
										<option value="AB">Alberta </option>
										<option value="AZ">Arizona </option>
										<option value="AR">Arkansas </option>
										<option value="BC">British Columbia </option>
										<option value="CA">California </option>
										<option value="CO">Colorado </option>
										<option value="CT">Connecticut </option>
										<option value="DE">Delaware </option>
										<option value="DC">District of Columbia </option>
										<option value="FL">Florida </option>
										<option value="GA">Georgia </option>
										<option value="HI">Hawaii </option>
										<option value="ID">Idaho </option>
										<option value="IL">Illinois </option>
										<option value="IN">Indiana </option>
										<option value="IA">Iowa </option>
										<option value="KS">Kansas </option>
										<option value="KY">Kentucky </option>
										<option value="LA">Louisiana </option>
										<option value="ME">Maine </option>
										<option value="MB">Manitoba </option>
										<option value="MD">Maryland </option>
										<option value="MA">Massachusetts </option>
										<option value="MI">Michigan </option>
										<option value="MN">Minnesota </option>
										<option value="MS">Mississippi </option>
										<option value="MO">Missouri </option>
										<option value="MT">Montana </option>
										<option value="NE">Nebraska </option>
										<option value="NV">Nevada </option>
										<option value="NB">New Brunswick </option>
										<option value="NH">New Hampshire </option>
										<option value="NJ">New Jersey </option>
										<option value="NM">New Mexico </option>
										<option value="NY">New York </option>
										<option value="NF">Newfoundland </option>
										<option value="NC">North Carolina </option>
										<option value="ND">North Dakota </option>
										<option value="NT">Northwest Territories </option>
										<option value="NS">Nova Scotia </option>
										<option value="OH">Ohio </option>
										<option value="OK">Oklahoma </option>
										<option value="ON">Ontario </option>
										<option value="OR">Oregon </option>
										<option value="PA">Pennsylvania </option>
										<option value="PE">Prince Edward Island </option>
										<option value="PR">Puerto Rico </option>
										<option value="QC">Quebec </option>
										<option value="RI">Rhode Island </option>
										<option value="SK">Saskatchewan </option>
										<option value="SC">South Carolina </option>
										<option value="SD">South Dakota </option>
										<option value="TN">Tennessee </option>
										<option value="TX">Texas </option>
										<option value="UT">Utah </option>
										<option value="VT">Vermont </option>
										<option value="VA">Virginia </option>
										<option value="WA">Washington </option>
										<option value="WV">West Virginia </option>
										<option value="WI">Wisconsin </option>
										<option value="WY">Wyoming </option>
										<option value="YT">Yukon </option>
									</select>
								</p>
								<p>
									<label>Country: </label>
									<select name="Country">
										<option value="">Select Country...</option>
										<option value="Canada">Canada</option>
										<option value="United States">United States</option>
									</select>
								</p>
								<p>
									<label>Zip/Postal Code: </label>
									<input type="text" name="zipcode" value="" />
								</p>
								<p>
									<label>Phone Number: </label>
									<input type="text" name="phone" value="" />
								</p>
								<p>
									<label>Email Address: </label>
									<input type="text" name="email" value="" />
								</p>
								<input type="submit" value="Register" class="register-button" />
							</fieldset>
						</form>
					</div>
				</div>
			</div>
			<div class="grid_5">
				<div class="box">
					<h2>
						<a href="#" id="toggle-search">Search</a>
					</h2>
					<div class="block" id="search">
						<form method="get" action="" class="search">
							<p>
								<input class="search text" name="value" type="text" />
								<input class="search button" value="Search" type="submit" />
							</p>
						</form>
					</div>
				</div>
				<div class="box">
					<h2>
						<a href="#" id="toggle-login-forms">Login Forms</a>
					</h2>
					<div class="block" id="login-forms">
						<form action="">
							<fieldset class="login">
								<legend>Login</legend>
								<p class="notice">Login to complete your purchase.</p>
								<p>
									<label>Username: </label>
									<input type="text" name="username" />
								</p>
								<p>
									<label>Password: </label>
									<input type="password" name="password" />
								</p>
								<input class="login button" type="submit" value="Login" />
							</fieldset>
						</form>
						<form action="">
							<fieldset>
								<legend>Register</legend>
								<p>If you do not already have an account, please create a new account to register.</p>
								<input type="submit" value="Create Account" />
							</fieldset>
						</form>
					</div>
				</div>
				<div class="box articles">
					<h2>
						<a href="#" id="toggle-articles">Articles</a>
					</h2>
					<div class="block" id="articles">
						<div class="first article">
							<h3>
								<a href="#">Article Heading</a>
							</h3>
							<h4>Subheading</h4>
							<p class="meta">Vancouver, BC — Wednesday, 23 April 2008</p>
							<a href="#" class="image">
								<img src="img/photo_60x60.jpg" width="60" height="60" alt="photo" />
							</a>
							<p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat. <a href="#">Visit site.</a></p>
						</div>
						<div class="article">
							<h3>
								<a href="#">Article Heading</a>
							</h3>
							<h4>Subheading</h4>
							<p class="meta">Vancouver, BC — Wednesday, 23 April 2008</p>
							<a href="#" class="image">
								<img src="img/photo_60x60.jpg" width="60" height="60" alt="photo" />
							</a>
							<p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat. <a href="#">Visit site.</a></p>
						</div>
						<div class="article">
							<h1>Heading 1</h1>
							<h2>Heading 2</h2>
							<h3>Heading 3</h3>
							<h4>Heading 4</h4>
							<p class="meta">Vancouver, BC — Wednesday, 23 April 2008</p>
							<a href="#" class="image">
								<img src="img/photo_60x60.jpg" width="60" height="60" alt="photo" />
							</a>
							<p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat. <a href="#">Visit site.</a></p>
							<h5>Heading 5</h5>
							<p>Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis at vero eros et accumsan et iusto odio dignissim qui blandit praesent luptatum zzril delenit augue duis dolore te feugait nulla facilisi.</p>
							<h6>Heading 6</h6>
							<p>Epsum factorial non deposit quid pro quo hic escorol. Olypian quarrels et gorilla congolium sic ad nauseum. Souvlaki ignitus carborundum e pluribus unum.</p>
						</div>
					</div>
				</div>
			</div>