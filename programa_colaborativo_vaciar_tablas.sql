delete from prog_comentarios      ;
delete from prog_enmiendas        ;
delete from prog_likes_comentarios;
delete from prog_likes_enmiendas  ;
delete from prog_likes_propuesta  ;

delete from prog_propuestas       ;
-- Para dejar las propuestas y poner los votos a 1, comentar el delete anterior y descomentar el UPDATE
-- UPDATE prog_propuestas set sum_likes=1, positivos=1, negativos=0, comentarios=0;



-- delete from users                 ;

drop table prog_comentarios;
drop table prog_enmiendas;
drop table prog_likes_comentarios       ;
drop table prog_likes_enmiendas;
drop table prog_likes_propuesta;
drop table prog_propuestas;
drop table users;
drop table users_can_participate;
