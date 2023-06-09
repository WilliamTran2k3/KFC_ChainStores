<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?php echo $title_page ?></title>
    <link rel="icon" href="<?php echo _WEB_ROOT; ?>/public/assets/server/img/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="<?php echo _WEB_ROOT; ?>/public/assets/client/css/root.css" />
    <link rel="stylesheet" href="<?php echo _WEB_ROOT; ?>/public/assets/client/css/bottomNavbar.css" />
    <link rel="stylesheet" href="<?php echo _WEB_ROOT; ?>/public/assets/client/css/style.css" />
    <?php if (!empty($link_css)) echo $link_css; ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css" integrity="sha512-SzlrxWUlpfuzQ+pcUCosxcglQRNAq/DZjVsC0lE40xsADsfeQoEypE+enwcOiGjk/bSuGGKHEyjSoQ1zVisanQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js" integrity="sha512-pumBsjNRGGqkPzKHndZMaAG+bir374sORyzM3uulLV14lN5LyykqNk8eEeUlUkB3U0M4FApyaHraT65ihJhDpQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script>
        $(function() {
            $(".wrap-loading").css("display", "none");
            // Dùng fetch API hiển thị tất cả phim ở trang phim đang chiếu
            $("#all-film-showing").click(function() {
                // Fetch API gọi để hiển thị tất cả phim
                fetch("<?php echo _WEB_ROOT ?>/film/getAllFilm")
                    .then((res) => res.text())
                    .then((data) => {
                        console.log(data);
                        $(".row-phim").children().remove();
                        $(".row-phim").append(data);
                    })
                    .catch((err) => console.log(err));
            });
            // Dùng fetch API hiển thị phim 3d ở trang phim đang chiếu
            $("#film-3d-showing").click(function() {
                // Fetch API gọi để lọc phim 3d
                fetch("<?php echo _WEB_ROOT ?>/film/getAllFilm3D")
                    .then((res) => res.text())
                    .then((data) => {
                        console.log(data);
                        $(".row-phim").children().remove();
                        $(".row-phim").append(data);
                    })
                    .catch((err) => console.log(err));
            });

            // Dùng fetch API hiển thị tất cả phim ở trang phim sắp chiếu
            $("#all-film-coming").click(function() {
                // Fetch API gọi để hiển thị tất cả phim
                fetch("<?php echo _WEB_ROOT ?>/film/getAllFilmComing")
                    .then((res) => res.text())
                    .then((data) => {
                        console.log(data);
                        $(".row-phim").children().remove();
                        $(".row-phim").append(data);
                    })
                    .catch((err) => console.log(err));
            });
            // Dùng fetch API hiển thị phim 3d ở trang phim sắp chiếu
            $("#film-3d-coming").click(function() {
                // Fetch API gọi để lọc phim 3d
                fetch("<?php echo _WEB_ROOT ?>/film/getAllFilm3DComing")
                    .then((res) => res.text())
                    .then((data) => {
                        console.log(data);
                        $(".row-phim").children().remove();
                        $(".row-phim").append(data);
                    })
                    .catch((err) => console.log(err));
            });
            $('.seat').click((e) => {
                if (!$(e.target).hasClass('sold'))
                    $(e.target).toggleClass('choosing');
            });
            $("#checkout").click(function() {
                $(".wrap-loading").css('display', 'block');
                const data = {
                    lichsu: {
                        soluongve: JSON.parse(sessionStorage.getItem("gheDaChon")).length,
                        ghedat: sessionStorage.getItem("gheHienThi"),
                        tongtien: $(".price-total-ticket").text().replaceAll(".", "").replace(" VND", "")
                    },
                    data_datcho: sessionStorage.getItem('gheDaChon')
                }
                fetch("<?php echo _WEB_ROOT ?>/booking/checkout", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify(data)

                    })
                    .then(res => res.text())
                    .then((data) => {
                        if (data) {
                            window.location.href = data;
                        } else {
                            alert("Thất bại");
                        }
                        $(".wrap-loading").css('display', 'none');
                    })
                    .catch((err) => console.log(err));
            });
            $("#search-input").keypress(function(event) {
                // Nếu là phím enter thì gọi fetch API
                if (event.keyCode === 13) {
                    let val = $("#search-input").val();
                    if (val.trim() !== "") {
                        fetch(`<?php echo _WEB_ROOT ?>/film/search/${val}`)
                            .then(res => res.text())
                            .then((data) => {
                                $(".row-phim").children().remove();
                                $(".row-phim").append(data);
                            })
                    }
                }
            })

            $(".update-btn").click(function() {
                let name = $("#name-update").val();
                let email = $("#email-update").val();
                let birthday = $("#birthday-update").val();
                if (name.trim() !== '' && birthday.trim() !== '' && email.trim() !== '') {
                    let pattern = /^(0[1-9]|[1-2][0-9]|3[0-1])-(0[1-9]|1[0-2])-[0-9]{4}$/;
                    if (!pattern.test(birthday)) {
                        $(".modal-body").text("Định dạng ngày tháng không hợp lệ");
                        $("#modalInfo").modal("show");
                    } else {

                        let parts = birthday.split("-");
                        let formattedDate = parts[2] + "-" + parts[1] + "-" + parts[0];
                        fetch(`<?php echo _WEB_ROOT ?>/user/update/${name}/${email}/${formattedDate}`)
                            .then(res => res.text())
                            .then((data) => {
                                $(".user-name").text(name);
                                $(".modal-body").text("Cập nhật thông tin thành công");
                                $("#modalInfo").modal("show");
                                $(".edit-btn").removeClass("disabled");
                                $(".update-btn").addClass("disabled");
                                $(".detail-info input").prop('readonly', true);
                            })
                    }
                } else {
                    $(".msg-error-info").text("Chưa nhập đủ thông tin");
                }
            })
        })
    </script>

</head>

<body>
    <div class="wrap-loading">
        <div id="circle_square">
            <span></span>
            <span></span>
            <span></span>
            <span></span>
        </div>
    </div>
    <?php
    $this->render("blocks/navbar");
    ?>
    <section class="my-section">
        <div class="container mb-5">
            <?php
            if (!empty($loaimon))
                $this->render('order/order', ['loaimon' => $loaimon]);
            if (!empty($content))
                $this->render($content);
            ?>
        </div>

    </section>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.3/jquery.min.js" integrity="sha512-STof4xm1wgkfm7heWqFJVn58Hm3EtS31XFaagaa8VMReCXAkQnJZ+jEy8PCC/iT18dFy95WcExNHFTqLyp72eQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js" integrity="sha384-mQ93GR66B00ZXjt0YO5KlohRA5SY2XofN4zfuZxLkoj1gXtW8ANNCe9d5Y3eG5eD" crossorigin="anonymous"></script>
    <script src="<?php echo _WEB_ROOT; ?>/public/assets/client/js/script.js"></script>
    <?php if (!empty($link_script)) echo $link_script; ?>
</body>

</html>