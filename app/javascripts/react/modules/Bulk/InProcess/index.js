import React, { useContext, useEffect, useRef, useState } from "react";
import styled from "styled-components";
import Swal from "sweetalert2";
import { get, isNil, isNaN } from "lodash";
const { __ } = wp.i18n;
import Modal from "../../../ui/Modal";
import ModalOverlay from "../../../ui/Modal/Overlay";
import ModalIconClose from "../../../ui/Modal/IconClose";
import { BulkSettingsContext } from "../../../contexts/BulkSettingsContext";
import LimitExcedeed from "../../../components/Bulk/LimitExcedeed";
import { BulkProcessContext } from "../../../contexts/BulkProcessContext";
import LoadingImages from "../../../components/LoadingImages";
import {
	getCurrentBulk,
	getPreviewBulk,
	stopCurrentProcess,
} from "../../../services/ajax/current-bulk";
import useOnClickOutside from "../../../hooks/useOnClickOutside";
import { fromUnixTime } from "date-fns";
import { differenceInSeconds } from "date-fns/esm";

const SCContainerProcess = styled.div`
	padding: 32px;
	border-radius: 8px;
	border: solid 1px #2b68d9;
	border-color: ${({ finish, limitExcedeed }) => {
		if (limitExcedeed) {
			return `#fa7455`;
		}
		if (finish) {
			return `#27c166`;
		}
		return `#2b68d9`;
	}};
	background-color: #fafafc;
	position: relative;
	z-index: 3;
	&:after {
		position: absolute;
		content: "";
		z-index: 1;
		height: 100%;
		opacity: 0.1;
		background-color: ${({ finish, limitExcedeed }) => {
			if (limitExcedeed) {
				return `#fa7455`;
			}
			if (finish) {
				return `#27c166`;
			}
			return `#2b68d9`;
		}};
		width: ${({ percent }) => Number(percent).toFixed(0)}%;
		top: 0;
		left: 0;
		border-top-left-radius: 8px;
		border-bottom-left-radius: 8px;
	}
	h2 {
		margin: 0 0 8px;
		font-size: 24px;
		font-weight: bold;
		color: #001f59;
		display: flex;
		align-items: center;
		img {
			margin-left: 10px;
		}
	}
	.infos__images {
		font-size: 14px;
		color: #001f59;
	}
	.progress__bar {
		width: 100%;
		height: 8px;
		border-radius: 15px;
		background-color: #dadae0;
		overflow: hidden;
		&--content {
			height: 8px;
			border-top-left-radius: 15px;
			border-bottom-left-radius: 15px;
			background-color: ${({ finish, limitExcedeed }) => {
				if (limitExcedeed) {
					return `#fa7455`;
				}
				if (finish) {
					return `#27c166`;
				}
				return `#2b68d9`;
			}};
			width: ${({ percent }) => {
				if (Number(percent).toFixed(0) === 0) {
					return 0;
				}
				return `calc(${Number(percent).toFixed(0)}% + 32px)`;
			}};
		}
	}
	.actions {
		display: flex;
		align-items: center;
		position: absolute;
		top: 32px;
		right: 32px;
		z-index: 5;
		> div {
			margin-right: 16px;
		}
	}
	.btn__action {
		font-size: 14px;
		font-weight: bold;
		text-align: center;
		border: 1px solid #d5d9dc;
		padding: 0px 16px;
		height: 40px;
		box-sizing: border-box;
		color: #00081a;
		border-radius: 4px;
		display: flex;
		align-items: center;
		justify-content: center;
		background-color: #fff;
		&--reload {
			display: flex;
			align-items: center;
		}
		&:hover {
			cursor: pointer;
			background-color: #fafafa;
		}
	}
`;

const SCContainerModal = styled.div`
	h2 {
		font-size: 24px;
		font-weight: bold;
		text-align: center;
		color: #00081a;
	}
	.item__image {
		display: flex;
		align-items: center;
		margin-bottom: 32px;
		img {
			width: 64px;
			height: 64px;
			margin-right: 16px;
		}
		p {
			margin: 0;
		}
		&--filename {
			font-size: 14px;
			color: #5b2222;
			margin-bottom: 4px;
		}
		&--alt {
			font-size: 16px;
			color: #00081a;
		}
	}
`;

const SCFinishProcess = styled.div`
	padding: 32px;
	border-radius: 8px;
	border: solid 1px #00081a;
	background-color: #fafafc;
	position: relative;
	z-index: 3;
	margin-bottom: 50px;
	p {
		font-size: 16px;
	}
	.btn__view {
		font-size: 14px;
		font-weight: bold;
		text-align: center;
		border: 1px solid #d5d9dc;
		padding: 0px 16px;
		height: 40px;
		box-sizing: border-box;
		color: #00081a;
		border-radius: 4px;
		display: inline-flex;
		align-items: center;
		justify-content: center;
		background-color: #fff;
		&:hover {
			cursor: pointer;
			background-color: #fafafa;
		}
	}
`;

const ModalCurrentResults = ({ onClickClose, state }) => {
	const ref = useRef(null);
	useOnClickOutside(ref, () => onClickClose());
	const [loading, setLoading] = useState(true);
	const [images, setImages] = useState([]);

	useEffect(() => {
		const fetchData = async () => {
			const { data } = await getPreviewBulk();

			setLoading(false);
			setImages(data);
		};

		fetchData();
	}, []);

	const optimizeFilename = get(
		state,
		"currentProcess.settings.optimizeFile",
		false
	);
	const optimizeAlt = get(
		state,
		"currentProcess.settings.optimizeAlt",
		false
	);

	return (
		<ModalOverlay>
			<div ref={ref}>
				<Modal style={{ padding: 32 }}>
					<SCContainerModal>
						<ModalIconClose onClick={onClickClose}>
							<img
								src={`${IMAGESEO_URL_DIST}/images/cross-grey.svg`}
							/>
						</ModalIconClose>
						<h2>{__("Results details", "imageseo")}</h2>
						<div
							style={{
								color: "#4c525e",
								fontSize: 16,
								textAlign: "center",
								lineHeight: 1.5,
							}}
						>
							{__(
								"Here is the preview of 5 randoms alt tags generated by our AI.  You can manually edit them in the WordPress media library.",
								"imageseo"
							)}
						</div>
						<div
							style={{
								position: "relative",
								marginTop: 16,
								minHeight: 200,
							}}
						>
							{loading && <LoadingImages />}
							{images.map((image, key) => {
								let filename = __(
									"Sorry, no filename found",
									"imageose"
								);
								let alt = "";
								if (get(image, "report.success")) {
									filename = `${get(
										image,
										"report.filename"
									)}.${get(image, "report.extension")}`;
									alt = `${get(image, "report.alt")}`;
								}
								return (
									<div className="item__image" key={key}>
										<img src={image.url} />
										<div>
											{optimizeFilename && (
												<p className="item__image--filename">
													{__(
														"Filename alias:",
														"imageseo"
													)}{" "}
													{filename}
												</p>
											)}

											{optimizeAlt && (
												<p className="item__image--alt">
													{__("Alt:", "imageseo")}{" "}
													{alt}
												</p>
											)}
										</div>
									</div>
								);
							})}
						</div>
					</SCContainerModal>
				</Modal>
			</div>
		</ModalOverlay>
	);
};

const BulkInProcess = () => {
	const { state, dispatch } = useContext(BulkProcessContext);

	let total_ids_optimized = 0;
	let total_images = 1;

	if (!state.bulkIsFinish) {
		total_ids_optimized = get(
			state,
			"currentProcess.id_images_optimized",
			[]
		).length;

		total_images = get(state, "currentProcess.total_images", 0);
	} else {
		total_ids_optimized = get(
			state,
			"finishProcess.id_images_optimized",
			[]
		).length;

		total_images = get(state, "finishProcess.total_images", 0);
	}

	const percent = Number((total_ids_optimized * 100) / total_images).toFixed(
		2
	);

	const [isOpen, setIsOpen] = useState(false);
	const [reload, setReload] = useState(false);
	const [nextProcessed, setNextProcessed] = useState(
		get(IMAGESEO_DATA, "NEXT_SCHEDULED", false)
	);
	const limit = get(IMAGESEO_DATA, "LIMIT_EXCEDEED", false);

	const [limitExcedeed, setLimitExcedeed] = useState(limit ? true : false);
	const onClickClose = () => setIsOpen(false);

	const handleStopBulk = () => {
		Swal.fire({
			title: __("Are you sure?", "imageseo"),
			text: __(
				"You will always be able to pick up where the process left off.",
				"imageseo"
			),
			icon: "info",
			showCancelButton: true,
			confirmButtonColor: "#3085d6",
			confirmButtonText: __("Stop process", "imageseo"),
		}).then(async (result) => {
			if (result.value) {
				const { data } = await stopCurrentProcess();
				dispatch({ type: "STOP_CURRENT_PROCESS", payload: data });
			}
		});
	};

	const handleRefreshData = async () => {
		setReload(true);
		const { data } = await getCurrentBulk();
		if (get(data, "current", false)) {
			dispatch({
				type: "UPDATE_CURRENT_PROCESS",
				payload: get(data, "current"),
			});
			setNextProcessed(get(data, "scheduled", false));
		} else if (get(data, "finish", false)) {
			dispatch({
				type: "FINISH_CURRENT_PROCESS",
				payload: get(data, "finish"),
			});
		}

		if (get(data, "limit_excedeed", false)) {
			setLimitExcedeed(true);
			dispatch({
				type: "UPDATE_LIMIT_EXCEDEED",
				payload: true,
			});
		}

		setReload(false);
	};

	const optimizeAlt = get(
		state,
		"currentProcess.settings.optimizeAlt",
		false
	);
	const optimizeFilename = get(
		state,
		"currentProcess.settings.optimizeFile",
		false
	);

	let diffSeconds = false;
	try {
		if (nextProcessed) {
			const now = new Date();
			diffSeconds = differenceInSeconds(fromUnixTime(nextProcessed), now);
			if (diffSeconds < 0) {
				diffSeconds = Math.abs(diffSeconds);
			}
		}
	} catch (error) {}

	return (
		<>
			{isOpen && (
				<ModalCurrentResults
					onClickClose={onClickClose}
					state={state}
				/>
			)}
			<SCContainerProcess
				percent={percent}
				finish={state.bulkIsFinish}
				limitExcedeed={limitExcedeed}
			>
				<h2>
					{__("Bulk optimization in progress", "imageseo")}{" "}
					{!state.bulkIsFinish && !limitExcedeed && (
						<img
							src={`${IMAGESEO_URL_DIST}/images/rotate-cw.svg`}
							style={{
								animation:
									"imageseo-rotation 1s infinite linear",
							}}
						/>
					)}
				</h2>
				{!isNaN(diffSeconds) &&
					Number(diffSeconds) > 0 &&
					Number(diffSeconds) < 60 && (
						<p>
							{__("Next process in few seconds", "imageseo")} (
							{diffSeconds}s){" "}
						</p>
					)}
				<p className="infos__images">
					{total_ids_optimized} / {total_images} images{" "}
					{!isNaN(percent) && <>- {percent}%</>}
				</p>
				{optimizeFilename && (
					<p>{__("Optimize Filename: Yes", "imageseo")}</p>
				)}
				{optimizeAlt && (
					<p>{__("Optimize Alternative text: Yes", "imageseo")}</p>
				)}
				<div className="progress__bar">
					<div className="progress__bar--content"></div>
				</div>
				<div className="actions">
					{!state.bulkIsFinish && !limitExcedeed && (
						<div
							className="btn__action btn__action--reload"
							onClick={handleRefreshData}
						>
							{__("Click here for refresh data", "imageseo")}
							{reload && (
								<img
									src={`${IMAGESEO_URL_DIST}/images/rotate-cw.svg`}
									style={{
										marginLeft: 5,
										animation:
											"imageseo-rotation 1s infinite linear",
									}}
								/>
							)}
						</div>
					)}
					<div
						className="btn__action"
						onClick={() => setIsOpen(true)}
					>
						{__("View results", "imageseo")}
					</div>
					{!state.bulkIsFinish && !limitExcedeed && (
						<div className="btn__action" onClick={handleStopBulk}>
							<img
								src={`${IMAGESEO_URL_DIST}/images/icon-pause.svg`}
								alt=""
							/>
							{__("Pause", "imageseo")}
						</div>
					)}
				</div>
			</SCContainerProcess>
			{state.bulkIsFinish && (
				<>
					<SCFinishProcess>
						<p>{__("The process ended well.", "imageseo")}</p>
						<p>
							{__(
								"You can edit and view all your results in your media library in 'list' mode.",
								"imageseo"
							)}
						</p>
						<a className="btn__view" href={IMAGESEO_LIBRARY_URL}>
							{__("View medias", "imageseo")}
						</a>
					</SCFinishProcess>
				</>
			)}
			{limitExcedeed && <LimitExcedeed percent={percent} />}
		</>
	);
};

export default BulkInProcess;
