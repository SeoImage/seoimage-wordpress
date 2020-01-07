const generateReport = async attachmentId => {
	const formData = new FormData();

	formData.append("action", "imageseo_generate_report");
	formData.append("attachmentId", attachmentId);

	const response = await fetch(ajaxurl, {
		method: "POST",
		body: formData
	});

	return await response.json();
};

export default generateReport;