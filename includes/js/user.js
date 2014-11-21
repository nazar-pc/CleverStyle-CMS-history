function login (login, password) {
	$.ajax(
		base_url+"/"+api+"/user/login",
		{
			type: 'post',
			cache: false,
			data: {
				login: hash('sha224', login)
			},
    		success: function(data) {
				if (data == 'false') {
					this.error();
				} else {
					alert(data);
				}
			},
    		error: function(data) {
				if (data) {
					alert(data);
				} else {
					alert("error");
				}
			}
		}
	);
}