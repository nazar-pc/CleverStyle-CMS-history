function login (login, password) {
	$.ajax(
		base_url+"/api/user/login",
		{
			type: 'post',
			cache: false,
			data: {
				login: hash('sha224', login)
			},
    		success: function(data) {
				alert(data);
			},
    		error: function() {
				alert("error");
			}
		}
	);
}