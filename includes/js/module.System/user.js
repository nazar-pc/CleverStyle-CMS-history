	function login (login, password) {
		$.ajax(
			base_url+"/"+api+"/user/login",
			{
				type: 'post',
				cache: false,
				data: {
					login: hash('sha224', login)
				},
				success: function(random_hash) {
					if (random_hash.length == 56) {
						$.ajax(
							base_url+"/"+api+"/user/login",
							{
								type: 'post',
								cache: false,
								data: {
									auth_hash: hash('sha512', hash('sha224', login)+hash('sha512', password)+navigator.userAgent+random_hash)
								},
								success: function(result) {
									if (result == 'true') {
										location.reload();
									} else {
										alert(result);
									}
								},
								error: function() {
									alert(auth_error_connection);
								}
							}
						);
					} else {
						alert(random_hash);
					}
				},
				error: function() {
					alert(auth_error_connection);
				}
			}
		);
	}