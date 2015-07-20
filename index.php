<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8"/>
		<link href="css/main.css" rel="stylesheet">
		<script type="text/javascript" src="js/short.js"></script>
		<title>URL shortener</title>
	</head>
	<body>
	<div class="content">
		<header>URL shortener</header>
		<form action="javascript:shortMe()">
			<table>
				<tr>
					<th>Long URL</th>
					<th>Short URL</th>
				</tr>
				<tr>
					<td>
						<input type="url" name="url" autofocus="autofocus">
						<input type="submit" value="Do!">
					</td>
					<td id=result><span id="spanResult"></span></td>
				</tr>
			</table>
		</form>
		<footer>
			<pre>
            Workflow:

            1. Site-visitor (V) enters any original URL to the Input field, like
            http://anydomain/any/path/etc;
            2. V clicks submit button;
            3. Page makes AJAX-request;
            4. Short URL appears in Span element, like http://yourdomain/abCdE (don't use any
               external APIs as goo.gl etc.);
            5. V can copy short URL and repeat process with another link

            Short URL should redirect to the original link in any browser from any place and keep
            actuality forever, doesn't matter how many times application has been used after that.


            Requirements:

            1. Use PHP;
            2. Don't use any frameworks.
			</pre>
		</footer>
	</div>
	</body>
</html>