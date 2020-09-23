{application, 'amqp10_common', [
	{description, "Modules shared by rabbitmq-amqp1.0 and rabbitmq-amqp1.0-client"},
	{vsn, "3.8.1"},
	{id, "v3.8.1"},
	{modules, ['amqp10_binary_generator','amqp10_binary_parser','amqp10_framing','amqp10_framing0']},
	{registered, []},
	{applications, [kernel,stdlib]},
	{env, []}
]}.